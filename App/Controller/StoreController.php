<?php declare(strict_types=1);
namespace App\Controller;

use App\Model\ReadStoreModel;
use PDO;

class StoreController {

    private $requestMethod, $askFor, $uriParameters;

    private $readStoreModel;
    const DaySet = ['Mon', 'Tues', 'Weds', 'Thurs', 'Fri', 'Sat', 'Sun'];

    public function __construct(PDO $pdo, $requestMethod, $askFor, $uriParameters)
    {
        $this->requestMethod = $requestMethod;
        $this->askFor = $askFor;
        $this->uriParameters = $uriParameters;

        $this->readStoreModel = new ReadStoreModel($pdo);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                switch ($this->askFor){
                    case 'storesOpenAt':
                        //http://127.0.0.1:8000/store/storesOpenAt?time=9:30
                        $time = $this->uriParameters['time'];
                        if ($time){
                            $result = $this->readStoreModel->storesOpenAt($time);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'storesOpenOnDayAt':
                        //http://127.0.0.1:8000/store/storesOpenOnDayAt?day=Mon&time=10:00
                        $day = $this->uriParameters['day'];
                        $time = $this->uriParameters['time'];
                        if ($day && $time && in_array($day, $this::DaySet)){
                            $result = $this->readStoreModel->storesOpenOnDayAt($day, $time);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'storesOpenMoreThanHoursPerDay':
                        //http://127.0.0.1:8000/store/storesOpenMoreThanHoursPerDay?hour=9
                        $check = isset($this->uriParameters['hour']);
                        $hour = $check ? intval($this->uriParameters['hour']) : null;
                        
                        if ($hour <=24 && $hour >= 0){
                            $result = $this->readStoreModel->storesOpenMoreThanHoursPerDay($hour);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'storesOpenMoreThanHoursPerWeek':
                        //http://127.0.0.1:8000/store/storesOpenMoreThanHoursPerWeek?hour=9
                        $check = isset($this->uriParameters['hour']);
                        $hour = $check ? intval($this->uriParameters['hour']) : null;
                        
                        if ($hour <=24*7 && $hour >= 0){
                            $result = $this->readStoreModel->storesOpenMoreThanHoursPerWeek($hour);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'storesHaveMoreXnumberBooks':
                        //http://127.0.0.1:8000/store/storesHaveMoreXnumberBooks?x=9
                        $check = isset($this->uriParameters['x']);
                        $x = $check ? intval($this->uriParameters['x']) : null;
                        
                        if ($x>=0){
                            $result = $this->readStoreModel->storesHaveMoreXnumberBooks($x);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'storesHaveLessYnumberBooks':
                        //http://127.0.0.1:8000/store/storesHaveLessYnumberBooks?y=9
                        $check = isset($this->uriParameters['y']);
                        $y = $check ? intval($this->uriParameters['y']) : null;
                        
                        if ($y>=0){
                            $result = $this->readStoreModel->storesHaveLessYnumberBooks($y);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'storesHaveMoreXnumberBooksPriceBetween':
                        //http://127.0.0.1:8000/store/storesHaveMoreXnumberBooksPriceBetween?x=9&lowPri=3.00&highPri=9.00
                        $checkX = isset($this->uriParameters['x']);
                        $checkLowPri = isset($this->uriParameters['lowPri']);
                        $checkHighPri = isset($this->uriParameters['highPri']);

                        $x = $checkX ? intval($this->uriParameters['x']) : null;
                        $lowPri = $checkLowPri ? floatval($this->uriParameters['lowPri']) : null;
                        $highPri = $checkHighPri ? floatval($this->uriParameters['highPri']) : null;
                        
                        
                        if ($x >= 0 && $lowPri && $highPri){
                            $result = $this->readStoreModel->storesHaveMoreXnumberBooksPriceBetween($x, $lowPri, $highPri);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'storesHaveLessYnumberBooksPriceBetween':
                        //http://127.0.0.1:8000/store/storesHaveLessYnumberBooksPriceBetween?y=9&lowPri=3.00&highPri=9.00
                        $checkX = isset($this->uriParameters['y']);
                        $checkLowPri = isset($this->uriParameters['lowPri']);
                        $checkHighPri = isset($this->uriParameters['highPri']);

                        $y = $checkX ? intval($this->uriParameters['y']) : null;
                        $lowPri = $checkLowPri ? floatval($this->uriParameters['lowPri']) : null;
                        $highPri = $checkHighPri ? floatval($this->uriParameters['highPri']) : null;
                        
                        
                        if ($y >= 0 && $lowPri && $highPri){
                            $result = $this->readStoreModel->storesHaveLessYnumberBooksPriceBetween($y, $lowPri, $highPri);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'searchStores':
                        //http://127.0.0.1:8000/store/searchStores?searchTerm=th
                        $check = isset($this->uriParameters['searchTerm']);
                        $searchTerm = $check ? $this->uriParameters['searchTerm'] : null;
                        
                        if (strlen($searchTerm) > 0){
                            
                            $result = $this->readStoreModel->searchStores($searchTerm);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    case 'topStoreRankByAmount':
                        //http://127.0.0.1:8000/store/topStoreRankByAmount?
                        
                        $result = $this->readStoreModel->topStoreRankByAmount();
                        
                        $response['status_code_header'] = 'HTTP/1.1 200 OK';
                        $response['body'] = json_encode($result);
                                              
                        break;
                    case 'topStoreRankByTrasactTimes':
                        //http://127.0.0.1:8000/store/topStoreRankByTrasactTimes?
                        
                        $result = $this->readStoreModel->topStoreRankByTrasactTimes();
                        
                        $response['status_code_header'] = 'HTTP/1.1 200 OK';
                        $response['body'] = json_encode($result);
                                                
                        break;
                    case 'totalNumAmountWithinDate':
                        //http://127.0.0.1:8000/store/totalNumAmountWithinDate?y=9&startDate=2020-01-01&endDate=2020-04-30
                       
                        $checkStartDate = isset($this->uriParameters['startDate']);
                        $checkEndDate = isset($this->uriParameters['endDate']);

                        $startDate = $checkStartDate ? $this->uriParameters['startDate'] : null;
                        $endDate = $checkEndDate ? $this->uriParameters['endDate'] : null;
                        
                        if ($startDate && $endDate){
                            $result = $this->readStoreModel->totalNumAmountWithinDate( $startDate, $endDate);
                            $response['status_code_header'] = 'HTTP/1.1 200 OK';
                            $response['body'] = json_encode($result);
                        }                        
                        break;
                    default:
                        $response = $this->notFoundResponse();
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

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}