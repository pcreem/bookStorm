<?php declare(strict_types=1);

namespace App\Controller;

use App\Middleware\Database\StoreSchema;
use App\Middleware\Database\ParseDatetime;

class DatabaseController implements StoreSchema
{
    private $pdo;
    private $user, $purchaseHistory, $stores, $books, $storeBook, $officeHours;

    public function __construct($pdo){
        $this->pdo = $pdo;
        // $this->createTables();
        $this->parseData();
        $this->importData();
    }

    public function createTables(){
        $this->pdo->query('
        DROP TABLE IF EXISTS Users, PurchaseHistory, Stores, StoreBook, Books, OfficeHours 
        ');

        $this->pdo->query('
        DROP PROCEDURE IF EXISTS CreateTables
        ');

        $this->pdo->query('
        CREATE PROCEDURE CreateTables()
        BEGIN
        
        CREATE TABLE IF NOT EXISTS Users
        (
        id int NOT NULL,
        userName varchar(255) NOT NULL,
        cashBalance float NOT NULL,
        PRIMARY KEY (id)
        );

        CREATE TABLE IF NOT EXISTS PurchaseHistory
        (
        id int NOT NULL AUTO_INCREMENT,
        bookName varchar(255) NOT NULL,
        storeName varchar(255) NOT NULL,
        transactionAmount float NOT NULL,
        transactionDate datetime NOT NULL,
        userId int NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (userId) REFERENCES Users(id)
        );

        CREATE TABLE IF NOT EXISTS Stores
        (
        id int NOT NULL AUTO_INCREMENT,
        storeName varchar(255) NOT NULL,
        cashBalance float NOT NULL,
        PRIMARY KEY (id)
        );

        CREATE TABLE IF NOT EXISTS Books
        (
        id int NOT NULL AUTO_INCREMENT,
        bookName varchar(255) NOT NULL,
        price float NOT NULL,
        PRIMARY KEY (id)
        );     

        CREATE TABLE IF NOT EXISTS StoreBook
        (
        id int NOT NULL AUTO_INCREMENT,
        booksId int NOT NULL,
        storesId int NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (booksId) REFERENCES Books(id),
        FOREIGN KEY (storesId) REFERENCES Stores(id)
        ); 
              
        CREATE TABLE IF NOT EXISTS OfficeHours
        (
        id int NOT NULL AUTO_INCREMENT,
        day ENUM("Mon","Tues","Weds","Thurs","Fri","Sat","Sun") NOT NULL,
        openTime time NOT NULL,
        closeTime time NOT NULL,
        storesId int NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (storesId) REFERENCES Stores(id)
        );
        
        END 
        ');

        $this->pdo->query('
        CALL CreateTables();
        ');

        $this->pdo->query('
        DROP PROCEDURE IF EXISTS CreateTables;
        ');
    }

    private function initProcess(string $filepath): string {
        $str = file_get_contents($filepath);
        $str = ltrim($str,"[");
        $str = substr_replace($str,"",strrpos($str,"]"));
        return $str;
    }

    private function organizeUserData($dissectUserStringData){
        $dissectObj = json_decode($dissectUserStringData); 
        $id = $dissectObj->id;
        $name = $dissectObj->name;
        $cashBalance = $dissectObj->cashBalance;
        $purchaseHistory = $dissectObj->purchaseHistory ?? null;

        return [$id, $name, $cashBalance, $purchaseHistory];
    }

    public function parseData(){
        //----------------------Process Store data------------------------------------
        $storeFilePath = __DIR__ . '/../../Data/book_store_data.json';
        $storeStr = $this->initProcess($storeFilePath);

        $parseDatetime = new ParseDatetime;
        $processedStoreData = [];
        $endpos = 5;
        $storeId = 1;
        while($endpos > 4){
            $endpos = strpos($storeStr,',',(int)strpos($storeStr,"storeName"));
            $dissectStore = substr($storeStr,0,(int)$endpos);
            
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
        $userFilePath = __DIR__ . '/../../Data/user_data.json';
        $userStr = $this->initProcess($userFilePath);

        $pattern = "/cashBalance/";
        $processedUserData = [];

        while(strlen($userStr) > 0){

            $endpos = strpos($userStr,"}",strpos($userStr,"}\n    ]\n")+1);
            $dissectUser = substr($userStr,0,$endpos+1);
            if(preg_match_all($pattern, $dissectUser)>1){
                $midpos = strrpos($dissectUser,"cashBalance")-7;
                $fisrtPart = rtrim(trim(substr($dissectUser,0,$midpos)),',');
                $secondPart = substr($dissectUser,$midpos);
                
                array_push($processedUserData,$this->organizeUserData($fisrtPart));
                array_push($processedUserData,$this->organizeUserData($secondPart));
            }
            else{
                array_push($processedUserData,$this->organizeUserData($dissectUser));
            }

            $userStr = substr($userStr,$endpos+3,strlen($userStr));
        }  

        //----------------------Generate tables data------------------------------------

        //$processedStoreData = [$storeId, $storeName,$storeCashBalance,$officeHoursArr($weekday/$opentime/$closetime),$booksArr(bookName/price)]
        //$processedUserData = [$id, $name, $cashBalance, $purchaseHistory(bookName/storeName/transactionAmount/transactionDate)]

        $user = [];
        $purchaseRaw = [];
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

        //----------------------collect tables------------------------------------
        //$user, $purchaseRaw, $stores, $books, $storeBook, $officeHours
        $this->user = $user;
        $this->purchaseHistory = $purchaseRaw;
        $this->stores = $stores;
        $this->books = $books;
        $this->storeBook = $storeBook;
        $this->officeHours = $officeHours;
    }

    public function importData(){
        $pdo = $this->pdo;

        $user = $this->user;
        $purchaseHistory = $this->purchaseHistory;
        $stores = $this->stores;
        $books = $this->books;
        $storeBook = $this->storeBook;
        $officeHours = $this->officeHours;

        $k = [$user, $purchaseHistory, $stores, $books, $storeBook, $officeHours];
        foreach($k as $t){
            print_r($t);
        }
    }
}
