<?php
// extract data into database 
// setup relation database
// write API document
// build API

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
        echo "$dissectObj->storeName \n"; 
        echo $dissectObj->openingHours; 
        // echo $dissectArr[0]->bookName;
        echo "\n---------------\n";
        
    }

    $str = substr($str,$endpos+2,strlen($str));
}
?>