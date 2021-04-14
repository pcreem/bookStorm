<?php declare(strict_types=1);
namespace App\Controller;

use App\Controller\StormAbstractClass;
use App\Model\UpdateModel;

class UpdateController extends StormAbstractClass{

    private $updateModel;
    
    public function processRequest(){
        $this->updateModel = new UpdateModel($this->pdo);
        switch ($this->requestMethod) {
            case 'PUT':
                switch ($this->askFor){
                    case 'updateStoreName':
                        //http://127.0.0.1:8000/store/updateStoreName
                        $checkStoreId = isset($this->uriParameters['storeId']);
                        $checkStoreName = isset($this->uriParameters['storeName']);

                        $storeId = $checkStoreId ? intval($this->uriParameters['storeId']) : null;
                        $storeName = $checkStoreName ? $this->uriParameters['storeName']: null;

                        if ($storeId && $storeName){
                            $result = $this->updateModel->updateStoreName($storeId, $storeName);
                            if ($result){
                                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                                $response['body'] = json_encode($result);
                            }
                            break;

                        }                        
                        break;
                    case 'updateBookName':
                        //http://127.0.0.1:8000/book/updateBookName
                        $checkBookId = isset($this->uriParameters['bookId']);
                        $checkBookName = isset($this->uriParameters['bookName']);

                        $bookId = $checkBookId ? intval($this->uriParameters['bookId']) : null;
                        $bookName = $checkBookName ? $this->uriParameters['bookName']: null;

                        if ($bookId && $bookName){
                            $result = $this->updateModel->updateBookName($bookId, $bookName);
                            if ($result){
                                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                                $response['body'] = json_encode($result);
                            }
                            break;
                            
                        }                        
                        break;
                    case 'updateBookPrice':
                        //http://127.0.0.1:8000/book/updateBookPrice
                        $checkBookId = isset($this->uriParameters['bookId']);
                        $checkBookPrice = isset($this->uriParameters['bookPrice']);

                        $bookId = $checkBookId ? intval($this->uriParameters['bookId']) : null;
                        $bookPrice = $checkBookPrice ? floatval($this->uriParameters['bookPrice']) : null;

                        if ($bookId && $bookPrice){
                            $result = $this->updateModel->updateBookPrice($bookId, $bookPrice);
                            if ($result){
                                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                                $response['body'] = json_encode($result);
                            }
                            break;
                            
                        }                        
                        break;
                    case 'updateUserName':
                        //http://127.0.0.1:8000/user/updateUserName
                        $checkUserId = isset($this->uriParameters['userId']);
                        $checkUserName = isset($this->uriParameters['userName']);

                        $userId = $checkUserId ? intval($this->uriParameters['userId']) : null;
                        $userName = $checkUserName ? $this->uriParameters['userName']: null;

                        if ($userId && $userName){
                            $result = $this->updateModel->updateUserName($userId, $userName);
                            if ($result){
                                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                                $response['body'] = json_encode($result);
                            }
                            break;                            
                        }                        
                        break;
                   
                }
            case 'POST':
                switch ($this->askFor){
                    case 'userPurchaseOneBook':
                        //http://127.0.0.1:8000/user/userPurchaseOneBook
                        $checkUserId = isset($this->uriParameters['userId']);
                        $checkStoreId = isset($this->uriParameters['storeId']);
                        $checkBookId = isset($this->uriParameters['bookId']);

                        $userId = $checkUserId ? intval($this->uriParameters['userId']) : null;
                        $storeId = $checkStoreId ? intval($this->uriParameters['storeId']) : null;
                        $bookId = $checkBookId ? intval($this->uriParameters['bookId']) : null;

                        if ($userId && $storeId && $bookId){
                            $result = $this->updateModel->userPurchaseOneBook($userId, $storeId, $bookId);
                            if ($result){
                                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                                $response['body'] = json_encode($result);
                            }
                            break;         
                        }                        
                        break;
                    
                }break;
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
            