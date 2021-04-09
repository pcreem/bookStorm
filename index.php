<?php
declare(strict_types=1);
require_once __DIR__.'/vendor/autoload.php';

use App\Model\ConnectDB;
use App\Controller\DatabaseController;

$pdo = ConnectDB::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$databaseController = new DatabaseController($pdo);

?>