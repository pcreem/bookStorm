<?php
namespace App\Middleware\Database;
$dir = __DIR__.'/../../../';
require_once $dir.'/vendor/autoload.php';
use Dotenv\Dotenv;
use PDO;
 
$dotenv = Dotenv::createImmutable($dir);
$dotenv->load();

class ConnectDB {
  private static $instance = null;
  private $conn; 

  private $host = '';
  private $dbname = '';
  private $user = '' ;
  private $password = '';
  
  private function __construct()
  {
    $this->host = $_ENV['DB_HOST'];
    $this->user = $_ENV['DB_USERNAME'];
    $this->password = $_ENV['DB_PASSWORD']; 
    $this->dbname = $_ENV['DB_DATABASE'];

    $this->conn = new PDO("mysql:host={$this->host};
    dbname={$this->dbname}", $this->user,$this->password,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
  }
  
  public static function getInstance()
  {
    if(!self::$instance)
    {
      self::$instance = new ConnectDB();
    }
    return self::$instance;
  }
  
  public function getConnection()
  {
    return $this->conn;
  }
}

  