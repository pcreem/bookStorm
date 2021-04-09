<?php declare(strict_types=1);

namespace App\Controller;
use App\Property\ReadUserBook;
use PDO;

class ReadUserBookController implements ReadUserBook
{
    private $pdo;
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function booksPriceBetween(float $lowPri, float $highPri){

        try{
            $sql='
                SELECT bookName, price 
                FROM Books 
                WHERE price BETWEEN :lowPri AND :highPri
                ORDER BY price      
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['lowPri' => $lowPri, 'highPri' => $highPri]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function topXusersByAmountWithinDate(int $x, string $startDate, string $endDate){

        try{
            $sql='
                SELECT P.userId, U.userName, ROUND(SUM(P.transactionAmount),2) AS amount  
                FROM PurchaseHistory P, Users U
                WHERE DATE(P.transactionDate) BETWEEN :startDate AND :endDate
                AND P.userId = U.id
                GROUP BY P.userId, U.userName
                ORDER BY amound DESC
                LIMIT :x     
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['x' => $x, 'startDate' => $startDate, 'endDate' => $endDate]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function usersAmountMoreWithinDate(float $amount, string $startDate, string $endDate){

        try{
            $sql='
                SELECT COUNT(userId) AS totalUser, ROUND(SUM(transactionAmount),2) AS amount
                FROM PurchaseHistory
                WHERE DATE(transactionDate) BETWEEN :startDate AND :endDate
                AND transactionAmount > :amount
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['amount' => $amount, 'startDate' => $startDate, 'endDate' => $endDate]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function usersAmountLessWithinDate(float $amount, string $startDate, string $endDate){

        try{
            $sql='
                SELECT COUNT(userId) AS totalUser, ROUND(SUM(transactionAmount),2) AS amount
                FROM PurchaseHistory
                WHERE DATE(transactionDate) BETWEEN :startDate AND :endDate
                AND transactionAmount < :amount
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['amount' => $amount, 'startDate' => $startDate, 'endDate' => $endDate]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;                
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    } 
}

