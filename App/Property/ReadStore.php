<?php declare(strict_types=1);

namespace App\Property;
use PDO;

interface ReadStore
{
    public function __construct(PDO $pdo);

    public function storesOpenAt(string $time);
    public function storesOpenOnDayAt(string $day, string $time);
    public function storesOpenMoreThanHoursPerDay(int $hour);
    public function storesOpenMoreThanHoursPerWeek(int $hour);
    public function storesHaveMoreXnumberBooks(int $x);
    public function storesHaveLessYnumberBooks(int $y);
    public function storesHaveMoreXnumberBooksPriceBetween(int $x, float $lowPri, float $highPri);
    public function storesHaveLessYnumberBooksPriceBetween(int $y, float $lowPri, float $highPri);
    public function searchStores(string $searchTerm);
    public function topStoreRankByAmount();
    public function topStoreRankByTrasactTimes();
    public function totalNumAmountWithinDate(string $startDate, string $endDate);
}