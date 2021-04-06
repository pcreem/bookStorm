<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Middleware\Database\ParseDatetime;

//----------------------Process Store data------------------------------------

function dataInitProcess(string $filepath): string {
    $str = file_get_contents($filepath);
    $str = ltrim($str,"[");
    $str = substr_replace($str,"",strrpos($str,"]"));
    return $str;
}

$storeFilePath = './book_store_data.json';
$storeStr = dataInitProcess($storeFilePath);

$parseDatetime = new ParseDatetime;
$processedStoreData = [];
$endpos = 5;
$storeId = 0;
while($endpos > 4){
    $endpos = strpos($storeStr,',',strpos($storeStr,"storeName"));
    $dissectStore = substr($storeStr,0,$endpos);
    
    if($endpos > 0){
        $dissectObj = json_decode($dissectStore); 
        $booksArr = $dissectObj->books;
        $datetimeArr = $parseDatetime->parseStoreDatetime($dissectObj->openingHours);
        
        $storeName = $dissectObj->storeName;
        $storeCashBalance = $dissectObj->cashBalance;

        array_push($processedStoreData,[$storeId, $storeName,$storeCashBalance,$datetimeArr,$booksArr]);
        $storeId++;
        // print_r($datetimeArr);
        // echo "$dissectObj->storeName \n"; 
        // echo "$dissectObj->cashBalance \n"; 

        // foreach($datetimeArr as $val){
        //     $weekday = $val[0];
        //     $opentime = $val[1];
        //     $closetime = $val[2];
        //     echo "$weekday---$opentime---$closetime\n";
        // }
        
        // foreach($booksArr as $val){
        //     echo "bookName: $val->bookName\n";
        //     echo "price: $val->price\n";
        // }
        // echo "\n---------------\n";
        
    }

    $storeStr = substr($storeStr,$endpos+2,strlen($storeStr));
}

print_r($processedStoreData);

//----------------------Process User data------------------------------------

function organizeUserData($dissectUserStringData){
    $dissectObj = json_decode($dissectUserStringData); 
    $id = $dissectObj->id;
    $name = $dissectObj->name;
    $cashBalance = $dissectObj->cashBalance;
    $purchaseHistory = $dissectObj->purchaseHistory ?? null;

    return [$id, $name, $cashBalance, $purchaseHistory];
}

$userFilePath = './user_data.json';
$userStr = dataInitProcess($userFilePath);

$pattern = "/cashBalance/";
$processedUserData = [];

while(strlen($userStr) > 0){

    $endpos = strpos($userStr,"}",strpos($userStr,"}\n    ]\n")+1);
    $dissectUser = substr($userStr,0,$endpos+1);
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

    $userStr = substr($userStr,$endpos+3,strlen($userStr));
}  

// foreach ($processedUserData as $val){

//     $id = $val[0];
//     $name = $val[1];
//     $cashBalance = $val[2];

//     echo "$id $name $cashBalance\n";

//     foreach ($val[3] as $purchaseHistory){
//         $bookName = $purchaseHistory->bookName;
//         $storeName = $purchaseHistory->storeName;
//         $transactionAmount = $purchaseHistory->transactionAmount;
//         $datetime = $parseDatetime->parseUserDatetime($purchaseHistory->transactionDate);
//         echo "$bookName $storeName $transactionAmount $datetime\n";
//     }

//     echo "------------------------\n";
// }

print_r($processedUserData);

?>