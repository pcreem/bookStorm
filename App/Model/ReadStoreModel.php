<?php declare(strict_types=1);

namespace App\Model;
use App\Property\ReadStore;
use PDO;

class ReadStoreModel implements ReadStore
{
    private $pdo;
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function storesOpenAt(string $time) {

        try{
            $sql='
                SELECT S.storeName, O.day, O.openTime 
                FROM Stores S, OfficeHours O 
                WHERE S.id = O.storesId AND O.openTime >= :time          
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['time' => $time]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function storesOpenOnDayAt(string $day, string $time) {

        try{
            $sql='
                SELECT S.storeName, O.day, O.openTime 
                FROM Stores S, OfficeHours O 
                WHERE S.id = O.storesId 
                AND O.openTime >= :time AND O.day = :day         
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['time' => $time, 'day' => $day]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function storesOpenMoreThanHoursPerDay(int $hour) {

        try{
            $sql='
                SELECT S.storeName, ABS(HOUR(O.closeTime) - HOUR(O.openTime)) as DayOfficeHour 
                FROM Stores S, OfficeHours O 
                WHERE S.id = O.storesId 
                AND ABS(HOUR(O.closeTime) - HOUR(O.openTime)) >= :hour     
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['hour' => $hour]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function storesOpenMoreThanHoursPerWeek(int $hour) {

        try{
            $sql='
                SELECT storeName, WeekOfficeHour
                FROM (
                SELECT S.storeName AS storeName, SUM(ABS(HOUR(O.closeTime) - HOUR(O.openTime))) as WeekOfficeHour 
                FROM Stores S, OfficeHours O 
                WHERE S.id = O.storesId
                GROUP BY S.storeName
                ) A
                WHERE WeekOfficeHour >= :hour     
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['hour' => $hour]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function storesHaveMoreXnumberBooks(int $x) {

        try{
            $sql='
                SELECT storeName, bookAmount 
                FROM (
                SELECT S.storeName AS storeName, COUNT(SB.booksId) as "bookAmount"
                FROM Stores S, StoreBook SB 
                WHERE S.id = SB.storesId
                GROUP BY S.storeName
                ) A
                WHERE bookAmount >= :x   
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['x' => $x]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function storesHaveLessYnumberBooks(int $y) {
        try{
            $sql='
                SELECT storeName, bookAmount 
                FROM (
                SELECT S.storeName AS storeName, COUNT(SB.booksId) as "bookAmount"
                FROM Stores S, StoreBook SB 
                WHERE S.id = SB.storesId
                GROUP BY S.storeName
                ) A
                WHERE bookAmount <= :y   
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['y' => $y]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function storesHaveMoreXnumberBooksPriceBetween(int $x, float $lowPri, float $highPri) {

        try{
            $sql='
                SELECT storeName, bookNumbers
                FROM (
                SELECT S.storeName AS storeName, COUNT(SB.booksId) AS "bookNumbers"
                FROM Stores S, StoreBook SB, Books B
                WHERE S.id = SB.storesId 
                AND SB.booksId = B.id 
                AND (B.price BETWEEN :lowPri AND :highPri)
                GROUP BY S.storeName
                ) A
                WHERE bookNumbers >= :x
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['x' => $x, 'lowPri' => $lowPri, 'highPri' => $highPri]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function storesHaveLessYnumberBooksPriceBetween(int $y, float $lowPri, float $highPri) {

        try{
            $sql='
                SELECT storeName, bookNumbers
                FROM (
                SELECT S.storeName AS storeName, COUNT(SB.booksId) AS "bookNumbers"
                FROM Stores S, StoreBook SB, Books B
                WHERE S.id = SB.storesId 
                AND SB.booksId = B.id 
                AND (B.price BETWEEN :lowPri AND :highPri)
                GROUP BY S.storeName
                ) A
                WHERE bookNumbers <= :y
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['y' => $y, 'lowPri' => $lowPri, 'highPri' => $highPri]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function searchStores(string $searchTerm) {
        
        try{
            $sql = "
                SELECT storeName, LENGTH('$searchTerm'), LENGTH(storeName),  
                (LENGTH('$searchTerm')/LENGTH(storeName))*100 AS relevance
                FROM Stores 
                WHERE storeName REGEXP '$searchTerm'
                ORDER BY relevance DESC            
            ";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function topStoreRankByAmount() {

        try{
            $sql='
                SELECT storeName, ROUND(SUM(transactionAmount),2) AS amount
                FROM PurchaseHistory
                GROUP BY storeName
                ORDER BY amount DESC
                LIMIT 1
            ';
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function topStoreRankByTrasactTimes(){

        try{
            $sql='
                SELECT storeName, COUNT(id) AS transactionNumber
                FROM PurchaseHistory
                GROUP BY storeName
                ORDER BY transactionNumber DESC
                LIMIT 1
            ';
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function totalNumAmountWithinDate(string $startDate, string $endDate){

        try{
            $sql='
                SELECT COUNT(id) AS transactionNumber, ROUND(SUM(transactionAmount),2) AS amount
                FROM PurchaseHistory
                WHERE DATE(transactionDate) BETWEEN :startDate AND :endDate
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
