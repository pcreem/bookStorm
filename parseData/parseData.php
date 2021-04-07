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
$storeId = 1;
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

//$processedStoreData = [$storeId, $storeName,$storeCashBalance,$officeHoursArr($weekday/$opentime/$closetime),$booksArr(bookName/price)]
//$processedUserData = [$id, $name, $cashBalance, $purchaseHistory(bookName/storeName/transactionAmount/transactionDate)]

$user = [];
$purchaseRaw = []; //not table
$purchase = [];
$purchaseId = 1;

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
            array_push($purchaseRaw,[$purchaseId, $bookName, $storeName ,$transactionAmount,$transactionDate,$userId]);
            $purchaseId++;
        }
    }
}

$stores = [];
$storeNameOnly = []; //not table
$books = [];
$bookNameOnly = []; //not table
$storeBook = [];
$officeHours = [];
$bookId = 1;

foreach ($processedStoreData as $storeData){
    $storeId = $storeData[0];
    $storeName = $storeData[1];
    $cashBalance = $storeData[2];
    $officeHoursArr = $storeData[3];
    $booksArr = $storeData[4];

    array_push($storeNameOnly, $storeName);
    array_push($stores, [$storeId, $storeName, $cashBalance]);  

    foreach ($officeHoursArr as $officeHoursData){
        $day = $officeHoursData[0];
        $opentime = $officeHoursData[1];
        $closetime = $officeHoursData[2];

        array_push($officeHours,[$day, $opentime, $closetime, $storeId]);
    }

    foreach ($booksArr as $bookData){
        $bookName = $bookData->bookName;
        $price = $bookData->price;

        array_push($bookNameOnly,$bookName);
        array_push($books,[$bookName,$price]);
        array_push($storeBook, [$bookId,$storeId]);
        $bookId++;
    }

}

//$purchaseRaw,[$purchaseId, $bookName, $storeName ,$transactionAmount,$transactionDate,$userId]

$purchaseBook = [];
$purchaseStore = [];

foreach ($purchaseRaw as $purchaseVal){
    $purchaseId = $purchaseVal[0];
    $purchBookName = $purchaseVal[1];
    $purchStoreName = $purchaseVal[2];
    $transactionAmount = $purchaseVal[3];

    $purchStoreId = array_search($purchStoreName,$storeNameOnly)+1;
    $purchBookId = array_search($purchBookName,$bookNameOnly)+1;

    foreach($storeBook as $sb){
        $bookId = $sb[0];
        $storeId = $sb[1];

        $count++;

        $purchStoreId === $storeId && $purchBookId === $bookId ? array_push($purchaseBook,[$bookId, $purchaseId]) : null;    
    }

    foreach($stores as $storesData){
        $storeId = $storesData[0];
        $storeName = $storesData[1];
        $purchStoreName === $storeName ? array_push($purchaseStore, [$storeId,$purchaseId]) : null;
    }

}

// There's a problem that some books can't find its belonged store in $purchaseBook.
// $a = count($purchaseRaw);
// $b = count($purchaseBook);
// $c = count($purchaseStore);
// echo "$a $b $c"; 173 168 173

//----------------------collect tables------------------------------------
//$user, $purchase, $stores, $books, $storeBook, $officeHours, $purchaseBook, $purchaseStore

print_r($purchaseStore); 
?>