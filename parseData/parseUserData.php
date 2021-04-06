<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Middleware\Database\ParseDatetime;

$parseDatetime = new ParseDatetime;

function organizeUserData($dissectUserStringData){
    $dissectObj = json_decode($dissectUserStringData); 
    $id = $dissectObj->id;
    $name = $dissectObj->name;
    $cashBalance = $dissectObj->cashBalance;
    $purchaseHistory = $dissectObj->purchaseHistory ?? null;

    return [$id, $name, $cashBalance, $purchaseHistory];
}

$str = file_get_contents("./user_data.json");
$str = ltrim($str,"[");
$str = substr_replace($str,"",strrpos($str,"]"));

$pattern = "/cashBalance/";
$processedUserData = [];

while(strlen($str) > 0){

    $endpos = strpos($str,"}",strpos($str,"}\n    ]\n")+1);
    $dissectUser = substr($str,0,$endpos+1);
    if(preg_match_all($pattern, $dissectUser)>1){
        $midpos = strrpos($dissectUser,"cashBalance")-7;
        $fisrtPart = rtrim(trim(substr($dissectUser,0,$midpos)),',');
        $secondPart = substr($dissectUser,$midpos);
        
        array_push($processedUserData,organizeUserData($fisrtPart));
        array_push($processedUserData,organizeUserData($secondPart));
    }
    else{
        array_push($processedUserData,organizeUserData($dissectUser));
    }

    $str = substr($str,$endpos+3,strlen($str));
}  

foreach ($processedUserData as $val){

    $id = $val[0];
    $name = $val[1];
    $cashBalance = $val[2];

    echo "$id $name $cashBalance\n";

    foreach ($val[3] as $purchaseHistory){
        $bookName = $purchaseHistory->bookName;
        $storeName = $purchaseHistory->storeName;
        $transactionAmount = $purchaseHistory->transactionAmount;
        $datetime = $parseDatetime->parseUserDatetime($purchaseHistory->transactionDate);
        echo "$bookName $storeName $transactionAmount $datetime\n";
    }

    echo "------------------------\n";
}

// print_r($processedUserData);

?>