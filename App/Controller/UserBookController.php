<?php declare(strict_types=1);
namespace App\Controller;

use App\Controller\StormAbstractClass;
use App\Model\ReadUserBookModel;

class UserBookController extends StormAbstractClass{

    private $readUserBookModel;
    
    public function processRequest(){
        $this->readUserBookModel = new ReadUserBookModel($this->pdo);
        switch ($this->requestMethod) {
            case 'GET':
                switch ($this->askFor){
                    case 'booksPriceBetween':
                        //http://127.0.0.1:8000/book/booksPriceBetween?lowPri=4.30&highPri=9.00
                        $checkLowPri = isset($this->uriParameters['lowPri']);
                        $checkHighPri = isset($this->uriParameters['highPri']);

                        $lowPri = $checkLowPri ? floatval($this->uriParameters['lowPri']) : null;
                        $highPri = $checkHighPri ? floatval($this->uriParameters['highPri']) : null;
                        if ($lowPri >= 0 && $highPri >= 0){
                            $result = $this->readUserBookModel->booksPriceBetween($lowPri, $highPri);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'usersAmountMoreWithinDate':
                        //http://127.0.0.1:8000/user/usersAmountMoreWithinDate?amount=3&startDate=2020-01-01&endDate=2020-09-30
                        $checkAmount = isset($this->uriParameters['amount']);
                        $checkStartDate = $this->validateDate($this->uriParameters['startDate']);
                        $checkEndDate = $this->validateDate($this->uriParameters['endDate']);

                        $amount = $checkAmount ? floatval($this->uriParameters['amount']) : null;
                        $startDate = $checkStartDate ? $this->uriParameters['startDate'] : null;
                        $endDate = $checkEndDate ? $this->uriParameters['endDate'] : null;
                        
                        if ($amount >= 0 && $startDate && $endDate){
                            $result = $this->readUserBookModel->usersAmountMoreWithinDate($amount, $startDate, $endDate);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'usersAmountLessWithinDate':
                        //http://127.0.0.1:8000/user/usersAmountLessWithinDate?amount=12.00&startDate=2020-01-01&endDate=2020-09-30
                        $checkAmount = isset($this->uriParameters['amount']);
                        $checkStartDate = $this->validateDate($this->uriParameters['startDate']);
                        $checkEndDate = $this->validateDate($this->uriParameters['endDate']);

                        $amount = $checkAmount ? floatval($this->uriParameters['amount']) : null;
                        $startDate = $checkStartDate ? $this->uriParameters['startDate'] : null;
                        $endDate = $checkEndDate ? $this->uriParameters['endDate'] : null;
                        
                        if ($amount >= 0 && $startDate && $endDate){
                            $result = $this->readUserBookModel->usersAmountLessWithinDate($amount, $startDate, $endDate);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                }
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
}
    
    