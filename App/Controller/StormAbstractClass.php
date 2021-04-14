<?php declare(strict_types=1);
namespace App\Controller;

use PDO;
use DateTime;

abstract class StormAbstractClass {

    protected $pdo, $requestMethod, $askFor, $uriParameters;

    public function __construct(PDO $pdo, $requestMethod, $askFor, $uriParameters)
    {
        $this->pdo = $pdo;
        $this->requestMethod = $requestMethod;
        $this->askFor = $askFor;
        $this->uriParameters = $uriParameters;
    }

    abstract public function processRequest();

    protected function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    protected function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}