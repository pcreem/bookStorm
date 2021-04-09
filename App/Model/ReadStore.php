<?php declare(strict_types=1);

namespace App\Model;
use PDO;

interface ReadStore
{
    public function __construct(PDO $pdo);

    public function storesOpenAt(string $time): array;
    public function storesOpenOnDayAt(string $day, string $time): array;
    public function storesOpenMoreThanHoursPerDay(int $hour): array;
    public function storesOpenMoreThanHoursPerWeek(int $hour): array;
    public function storesHaveMoreXnumberBooks(int $x): array;
    public function storesHaveLessYnumberBooks(int $y): array;
    public function storesHaveMoreXnumberBooksPriceBetween(int $x, float $lowPri, float $highPri): array;
    public function storesHaveLessYnumberBooksPriceBetween(int $y, float $lowPri, float $highPri): array;
    public function searchStores(string $storeName): string;
    public function topStoreRankByAmount(): string;
    public function topStoreRankByTrasactTimes(): string;
    public function totalNumAmountWithinDate(string $startDate, string $endDate): float;
}