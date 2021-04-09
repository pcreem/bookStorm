<?php declare(strict_types=1);

namespace App\Controller;
use App\Property\Update;
use PDO;

class UpdateController implements Update
{
    private $pdo;
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    
    public function updateStoreName(int $storeId, string $storeName){

        try{
            $sql='
                SELECT id FROM Stores WHERE id = :storeId     
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['storeId' => $storeId]);
            $check = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($check) > 0){
                $sql='
                    UPDATE Stores
                    SET storeName = :storeName
                    WHERE id = :storeId 
                ';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['storeId' => $storeId, 'storeName' => $storeName]);

                $sql='SELECT * FROM Stores WHERE id = :storeId';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['storeId' => $storeId]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;  
            }

            return 'data not exist';
                          
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function updateBookName(int $bookId, string $bookName){

        try{
            $sql='
                SELECT id FROM Books WHERE id = :bookId     
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['bookId' => $bookId]);
            $check = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($check) > 0){
                $sql='
                    UPDATE Books
                    SET bookName = :bookName
                    WHERE id = :bookId 
                ';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['bookId' => $bookId, 'bookName' => $bookName]);

                $sql='SELECT * FROM Books WHERE id = :bookId';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['bookId' => $bookId]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;  
            }
            
            return 'data not exist';
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function updateBookPrice(int $bookId, float $bookPrice){

        try{
            $sql='
                SELECT id FROM Books WHERE id = :bookId     
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['bookId' => $bookId]);
            $check = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($check) > 0){
                $sql='
                    UPDATE Books
                    SET price = :bookPrice
                    WHERE id = :bookId 
                ';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['bookId' => $bookId, 'bookPrice' => $bookPrice]);

                $sql='SELECT * FROM Books WHERE id = :bookId';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['bookId' => $bookId]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;  
            }
            
            return 'data not exist';
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function updateUserName(int $userId, string $userName){

        try{
            $sql='
                SELECT id FROM Users WHERE id = :userId     
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            $check = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($check) > 0){
                $sql='
                    UPDATE Users
                    SET userName = :userName
                    WHERE id = :userId 
                ';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['userId' => $userId, 'userName' => $userName]);
               
                $sql='SELECT * FROM Users WHERE id = :userId';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['userId' => $userId]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result; 
            }
            
            return 'data not exist';
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
  
    public function userPurchaseOneBook(int $userId,int $storeId,int $bookId){
        try{
            $this->pdo->query('DROP PROCEDURE IF EXISTS purchaseBook');

            $sql="
            CREATE PROCEDURE purchaseBook()
            BEGIN
                    
            DECLARE vStoreName VARCHAR(255);
            DECLARE vBookName VARCHAR(255);
            DECLARE vPrice FLOAT(5,2);
            DECLARE vUserCash FLOAT(5,2);

            DROP VIEW IF EXISTS bookInfo;

            CREATE VIEW bookInfo AS
            SELECT storeName, bookName, price
            FROM (
            SELECT S.id AS storeId, S.storeName AS storeName, B.id AS bookId, B.bookName AS bookName, B.price AS price
            FROM Books B, StoreBook SB, Stores S
            WHERE B.id = SB.booksId AND SB.storesId = S.id
            ) X
            WHERE storeId = $storeId AND bookId = $bookId;

            SELECT storeName INTO vStoreName FROM bookInfo;
            SELECT bookName INTO vBookName FROM bookInfo;
            SELECT price INTO vPrice FROM bookInfo;
            SELECT cashBalance INTO vUserCash FROM Users WHERE id=$userId;

            IF vUserCash - vPrice >= 0 THEN
            UPDATE Users
            SET cashBalance = vUserCash - vPrice
            WHERE id = $userId;
            
            INSERT INTO PurchaseHistory(bookName, storeName, transactionAmount, transactionDate, userId)
            VALUES (vBookName, vStoreName, vPrice, NOW(), $userId);
            END IF;

            DROP VIEW IF EXISTS bookInfo;

            END 
            ";

            $this->pdo->query($sql);
            $this->pdo->query('CALL purchaseBook'); 
            $this->pdo->query('DROP PROCEDURE IF EXISTS purchaseBook');

            $sql='
            SELECT * FROM PurchaseHistory 
            WHERE userId = :userId
            ORDER BY transactionDate DESC
            LIMIT 1
            ';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;

        }catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        
    }
}