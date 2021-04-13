<?php
declare(strict_types=1);
require_once __DIR__.'/vendor/autoload.php';

use App\Middleware\ConnectDB;
use App\Model\DatabaseModel;
use App\Model\ReadStoreModel;
use App\Model\ReadUserBookModel;
use App\Model\UpdateModel;

$pdo = ConnectDB::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// $databaseModel = new DatabaseModel($pdo);
$readStore = new ReadStoreModel($pdo);
$readUserBook = new ReadUserBookModel($pdo);
$update = new UpdateModel($pdo);

$test = $update->userPurchaseOneBook(1,2,20);
print_r($test);

?>