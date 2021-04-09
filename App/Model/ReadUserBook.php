<?php declare(strict_types=1);

namespace App\Model;
use PDO;

interface ReadUserBook
{
    public function __construct(PDO $pdo);

    public function booksPriceBetween(float $lowPri, float $highPri): array;

    public function topXusersByAmountWithinDate(int $x, string $startDate, string $endDate): array;
    public function usersAmountMoreWithinDate(float $amount, string $startDate, string $endDate): array;
    public function usersAmountLessWithinDate(float $amount, string $startDate, string $endDate): array; 
}