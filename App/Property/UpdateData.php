<?php declare(strict_types=1);

namespace App\Property;
use PDO;

interface UpdateData
{
    public function __construct(PDO $pdo);
    
    public function userPurchaseOneBook(int $userId,int $storeId,int $bookId);
    public function updateStoreName(int $storeId, string $storeName);
    public function updateBookName(int $bookId, string $bookName);
    public function updateBookPrice(int $bookId, float $bookPrice);
    public function updateUserName(int $userId, string $userName);

}