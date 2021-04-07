<?php 

namespace App\Middleware\Database;

interface StoreSchema
{
    public function __construct($pdo);

    public function createTables();

    public function parseData();

    public function importData();
}