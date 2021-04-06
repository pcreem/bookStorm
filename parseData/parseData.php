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
        
    }

    $storeStr = substr($storeStr,$endpos+2,strlen($storeStr));
}

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

//----------------------Generate tables data------------------------------------

//$processedStoreData = [$storeId, $storeName,$storeCashBalance,$datetimeArr($weekday/$opentime/$closetime),$booksArr(bookName/price)]
//$processedUserData = [$id, $name, $cashBalance, $purchaseHistory(bookName/storeName/transactionAmount/transactionDate)]

$user = [];
$purchase = [];

foreach ($processedUserData as $userdata){
    $userId = $userdata[0];
    $username = $userdata[1];
    $cashBalance = $userdata[2];
    $purchaseHistory = $userdata[3];

    array_push($user,[$userId,$username,$cashBalance]);
    if (count($purchaseHistory) > 0){
        foreach ($purchaseHistory as $purchaseData){
            $bookName = $purchaseData->bookName;
            $storeName = $purchaseData->storeName;
            $transactionAmount = $purchaseData->transactionAmount;
            $transactionDate = $parseDatetime->parseUserDatetime($purchaseData->transactionDate);

            array_push($purchase,[$transactionAmount,$transactionDate,$userId]);
        }
    }
}

print_r($user);


?>