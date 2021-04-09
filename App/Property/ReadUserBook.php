<?php declare(strict_types=1);

namespace App\Property;
use PDO;

interface ReadUserBook
{
    public function __construct(PDO $pdo);

    public function booksPriceBetween(float $lowPri, float $highPri);

    public function topXusersByAmountWithinDate(int $x, string $startDate, string $endDate);
    public function usersAmountMoreWithinDate(float $amount, string $startDate, string $endDate);
    public function usersAmountLessWithinDate(float $amount, string $startDate, string $endDate); 
}