<?php 

namespace App\Middleware\Database;

interface StoreSchema
{
    public function __construct($pdo);

    public function createTablesPrecedure();

    public function importData();
}