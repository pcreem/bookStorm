<?php declare(strict_types=1);

namespace App\Controller;

use App\Middleware\Database\StoreSchema;

class DatabaseController implements StoreSchema
{
    public $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
        $this->createTables();
    }

    public function createTables(){
        $this->pdo->query('
        DROP TABLE IF EXISTS Books, OfficeHours, Purchase, PurchaseBook, PurchaseStore, StoreBook, Stores, Users
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
        
        CREATE TABLE IF NOT EXISTS Purchase
        (
        id int NOT NULL AUTO_INCREMENT,
        transactionAmount float NOT NULL,
        transactionDate datetime NOT NULL,
        userId int NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (userId) REFERENCES Users(id)
        );
        
        CREATE TABLE IF NOT EXISTS PurchaseBook
        (
        id int NOT NULL AUTO_INCREMENT,
        booksId int NOT NULL,
        purchaseId int NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (booksId) REFERENCES Books(id),
        FOREIGN KEY (purchaseId) REFERENCES Purchase(id)
        );
        
        CREATE TABLE IF NOT EXISTS PurchaseStore
        (
        id int NOT NULL AUTO_INCREMENT,
        storesId int NOT NULL,
        purchaseId int NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (storesId) REFERENCES Stores(id),
        FOREIGN KEY (purchaseId) REFERENCES Purchase(id)
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

    public function importData(){}
}
