<?php
declare(strict_types=1);
require_once __DIR__.'/vendor/autoload.php';


use App\Middleware\ConnectDB;
use App\Controller\StoreController;
use App\Controller\UserBookController;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriPath = explode( '/', $uriPath);

const Storm = ['store', 'user', 'book'];
$storeOrUserBook = $uriPath[1];
$askFor = $uriPath[2] ?? null;
$uriParameters = [];

if (in_array($storeOrUserBook, Storm)) {

    $uriQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

    if (strlen($uriQuery) > 0){
        $uriQuery = explode( '&', $uriQuery );
        
        foreach ($uriQuery as $val){
            $parameters = explode( '=', $val );
            $uriParameters[$parameters[0]] = $parameters[1];
        }
    }    
}else {
    header("HTTP/1.1 404 Not Found");
    exit();  
}


$pdo = ConnectDB::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// $databaseModel = new DatabaseModel($pdo);
$readStore = new StoreController($pdo, $requestMethod, $askFor, $uriParameters);
$readUserBook = new UserBookController($pdo, $requestMethod, $askFor, $uriParameters);


$storeOrUserBook === 'store' ? $readStore->processRequest() : null;
$storeOrUserBook === 'user' || $storeOrUserBook === 'book' ? $readUserBook->processRequest() : null;


?>