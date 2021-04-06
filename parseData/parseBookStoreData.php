<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Middleware\Database\ParseDatetime;

$parseDatetime = new ParseDatetime;

$str = file_get_contents("./book_store_data.json");
$str = ltrim($str,"[");
$str = substr_replace($str,"",strrpos($str,"]"));
$endpos = 5;

//Process data into json formate
while($endpos > 4){
    $endpos = strpos($str,',',strpos($str,"storeName"));
    $dissectStore = substr($str,0,$endpos);
    
    if($endpos > 0){
        $dissectObj = json_decode($dissectStore); 
        $dissectArr = $dissectObj->books;
        $datetimeArr = $parseDatetime->parseStoreDatetime($dissectObj->openingHours);
        echo "$dissectObj->storeName \n"; 
        echo "$dissectObj->cashBalance \n"; 

        foreach($datetimeArr as $val){
            echo "$val[0]---$val[1]---$val[2]\n";
        }
        
        // echo $dissectArr[0]->bookName;
        foreach($dissectArr as $val){
            echo "bookName: $val->bookName\n";
            echo "price: $val->price\n";
        }
        echo "\n---------------\n";
        
    }

    $str = substr($str,$endpos+2,strlen($str));
}
?>