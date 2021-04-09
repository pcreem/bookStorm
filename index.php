<?php
declare(strict_types=1);
require_once __DIR__.'/vendor/autoload.php';

use App\Middleware\ConnectDB;
use App\Controller\DatabaseController;
use App\Controller\ReadStoreController;
use App\Controller\ReadUserBookController;
use App\Controller\UpdateController;

$pdo = ConnectDB::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// $databaseController = new DatabaseController($pdo);
$readStore = new ReadStoreController($pdo);
$readUserBook = new ReadUserBookController($pdo);
$update = new UpdateController($pdo);

$test = $update->userPurchaseOneBook(1,2,20);
print_r($test);

?>