<?php
/**
 * Magemonkey RMA
 *
 * RMA For The B2B Customer
 *
 * @category    RMA
 * @package     Magemonkey_RMA
 * @author      Anurag Chitnis<anurag@webtechsystem.com>
 * @version     1.0.0
 */
declare(strict_types=1);

namespace Magemonkey\RMA\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use Magemonkey\Core\Logger\Logger;
use Magemonkey\RMA\Helper\Constant;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magemonkey\RMA\Block\Adminhtml\Rma\Edit\Tab\View\PersonalInfo;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\ValidatorException;
use Magemonkey\LabelMessage\Helper\Data as TraslateHelper;

class Data extends AbstractHelper
{
    const CUSTOMER = "1";
    const RMA_ITEM_GENERATED = 'Generated';

    const PENDING = "0";

    public $commonTraslatemsg;

    /** 
     * @var Http 
     */
    private $request;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var RmaItemFactory
     */
    protected $itemFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var RmaFactory
     */
    protected $rma;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var TimezoneInterface
     */
    protected $date;

    protected $connection;

    protected $resource;

    /**
     * @var TokenFactory
     */
    protected $tokenModelFactory;

    /**
     * @var TraslateHelper
     */
    protected $traslateHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magemonkey\RMA\Model\RmaItemFactory $itemFactory,
        \Magemonkey\RMA\Model\RmaFactory $rma,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Logger $logger,
        TokenFactory $tokenModelFactory,
        PersonalInfo $rmaitemsinfo,
        ProductRepository $productRepository,
        Http $request,
        TraslateHelper $traslateHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->itemFactory = $itemFactory;
        $this->rma = $rma;
        $this->currentCustomer = $currentCustomer;
        $this->date = $date;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->logger->initLog(Constant::RMA_LOG_FILE);
        $this->_tokenModelFactory = $tokenModelFactory;
        $this->rmaitemsinfo = $rmaitemsinfo;
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->traslateHelper = $traslateHelper;
        parent::__construct($context);
    }

    /**
     * Insert Records
     *
     * @param string $table
     * @param array $data
     * @return void
     */
    public function insertMultiple($table, $data)
    {
        try {
            $tableName = $this->resource->getTableName($table);
            $this->connection->insertMultiple($tableName, $data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadSupportMedia($uploadedFiles)
    {
        if(count($uploadedFiles) > 1) {
            $filePath = [];
            if (count($uploadedFiles) < 6 && count($uploadedFiles) > 1) {
                foreach ($uploadedFiles as  $filevalue) {
                    $uploader = $this->fileUploaderFactory->create(['fileId' => $filevalue]);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $fileAdapter = $this->adapterFactory->create();
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setFilenamesCaseSensitivity(false);
                    try {
                        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                        $destinationPath = $mediaDirectory->getAbsolutePath('customer/rma');
                        $results = $uploader->save($destinationPath);
                        $filePath[] = $results;
                    } catch (Exception $e) {
                        return $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                return $this->messageManager->addWarning('Max 6 Media Allowed');
            }
            $path = [];
            foreach ($filePath as  $value) {
                $path [] = $value['file'];
            }
            $media = implode(",",$path);
           return  $media;
        }
    }

    /**
     * Update Items
     */
    public function updateItems($items, $fileInfo = null)
    {
        try {

            $entityIds = array_values($items["entity_id"]);
            if(array_key_exists('product_id',$items)) {
                $productIds = array_values($items["product_id"]);
                $count = max(count($entityIds), count($productIds));
            } else {
                $count = count($entityIds);
            }
            $model = $this->itemFactory->create();
            for ($i = 0; $i < $count; $i++) {
                if (isset($entityIds[$i])) {
                    $record = $model->load($entityIds[$i]);
                    $record->setData("qty", $items["qty"][$entityIds[$i]]);
                    $record->setData("reason", $items["reason"][$entityIds[$i]]);
                    //$record->setData("status", $items["status"][$entityIds[$i]]);
                    $record->setData("status", Constant::RMA_ITEM_STATUS);
                    $record->setData("order_id", $items["order_id"][$entityIds[$i]]);
                    $uploadedFiles = $fileInfo['support_media'][$entityIds[$i]];

                    if(!empty($uploadedFiles[$i]['name'])) {
                        $new =  $this->uploadSupportMedia($uploadedFiles);
                        $oldImages =  $record->getData('support_media');
                        if($oldImages) {
                            $updatedImages = $new.','.$oldImages;
                        }else {
                            $updatedImages = $new ;
                        }
                        $record->setData("support_media", $updatedImages);
                    }
                    $record->save();
                }
                $model->unsetData();
                if (isset($productIds[$i])) {
                    $model->setRmaId($items["rma_id"]);
                    $model->setProductId($productIds[$i]);
                    $model->setQty($items["qty"][$productIds[$i]]);
                    $uploadedFiles = $fileInfo['support_media'][$productIds[$i]];
                    $images =  $this->uploadSupportMedia($uploadedFiles);
                    $model->setSupportMedia($images);
                    $model->save();
                }
            }
            return $message = "Request Updated";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function insertItmes($items, $fileInfo = null)
    {
        try {

            $customerId = $this->currentCustomer->getCustomerId();
            $date = $this->date->date()->format('Y-m-d H:i:s');
            $rma = $this->rma->create();
            $rma->setData([
                'customer_id' => $customerId,
                'created_by'  => self::CUSTOMER,
                'created_at'  => $date,
                'status' => self::PENDING
            ]);
             $rma->save();
             $id = $rma->getRmaId();
             foreach ($items['product_id'] as $index => $productId) {

                $item = [
                    'rma_id'=> $rma->getRmaId(),
                    'product_id' => $productId,
                    'qty' => $items['qty'][$productId],
                    'reason' => $items['reason'][$productId],
                    //'status' => $items['status'][$productId],
                    'status' => Constant::RMA_ITEM_STATUS,
                    'order_id' => $items['order_id'][$productId],
                ];
                try {

                    $uploadedFiles = $fileInfo['support_media'][$productId];
                    $item['support_media'] = $this->uploadSupportMedia($uploadedFiles);

                } catch (\Exception $e) {

                    return  $this->messageManager->addError($e->getMessage());
                }
                    $result[] = $item;
             }

            $status =  $this->insertMultiple('magemonkey_rma_item', $result);
            if($status) {
                return $id;
            }else {
                return false;
            } 

        } catch (\Exception $e) {
            return  $this->messageManager->addError($e->getMessage());
        }
    }

    /**
     * Delete Item from list
     *
     * @param $id
     * @return void
     */
    public function deleteItem($id)
    {
        try {
            $item = $this->itemFactory->create();
            $item->load($id);
            $item->delete();
        } catch( \Exception $e) {
            return  $this->messageManager->addError($e->getMessage());
        }
    }

    /**
     * list RMA details
     *
     * @param array $data
     * @return string
     */
    
    public function rmaListdata($data)
    {
        $languageKey = $this->traslateHelper->getlanguageKey($data);
        $commonTraslateMessage = $this->traslateHelper->traslateMessages($languageKey);
        $this->commonTraslatemsg = $commonTraslateMessage;
        try{

            $responseArray = [];
            $this->logger->writeLog('============ RMA LIST API ==============');
            $jsonData = json_encode($data);
            $this->logger->writeLog('Request Data: ' . $jsonData);
            $authResult = $this->authnentication();
            if ($authResult !== null) {
                return json_encode($authResult, JSON_UNESCAPED_UNICODE);
            }
            $response = [];
            $collection = $this->rma->create()->getCollection();
            $collection->addFieldToFilter('rma_status', 1);
            if (isset($data['customer_id']) && isset($data['salesrep_id'])) {
                $collection->addFieldToFilter('customer_id', $data['customer_id']);
                $collection->addFieldToFilter('salesrep_id', $data['salesrep_id']);
                if ($collection->getSize() > 0) {
                    $response = $this->rmaListItems($collection);
                    $responseArray = $this->setResponse($response);
                } else {
                    $responseArray = $this->setResponse($response);
                }
            }
            if (isset($data['customer_id'])) {
                $collection->addFieldToFilter('customer_id', $data['customer_id']);
                if ($collection->getSize() > 0) {
                    $response = $this->rmaListItems($collection);
                    $responseArray = $this->setResponse($response);
                } else {
                    $responseArray = $this->setResponse($response);
                }
            }

            if (isset($data['salesrep_id'])) {
                $collection->addFieldToFilter('salesrep_id', $data['salesrep_id']);
                if ($collection->getSize() > 0) {
                    $response = $this->rmaListItems($collection);
                    $responseArray = $this->setResponse($response);
                } else {
                    $responseArray = $this->setResponse($response);
                }
            }
            $this->logger->writeLog("END RMA LIST API");
        } catch( \Exception $e) {
            $responseArray = [
                "responseCode" => Constant::RESPONSE_ERROR,
                "responseMessage" => $commonTraslateMessage['WebErrorMsg']
            ];
        }

        $this->logger->writeLog('============ END RMA LIST API ==============');

        return json_encode($responseArray, JSON_UNESCAPED_UNICODE);
    }

    /**
     * list RMA Items details
     *
     * @param string $param
     * @return string
     */

    public function rmaItemList($param){

        $languageKey = $this->request->getParam('languageKey');
        $commonTraslateMessage = $this->traslateHelper->traslateMessages($languageKey);
        $this->commonTraslatemsg = $commonTraslateMessage;

        try {
            
            $responseArray = [];
            $response = [];
            $this->logger->writeLog('============ RMA ITEMS LIST API ==============');
            $this->logger->writeLog('RMA ID: ' . $param);
            $authResult = $this->authnentication();
            if ($authResult !== null) {
                return json_encode($authResult, JSON_UNESCAPED_UNICODE);
            }
            if($param){
                $rmaCollection = $this->itemFactory->create()->getCollection();
                $rmaCollection->addFieldToFilter('rma_id', $param);
                if ($rmaCollection->getSize() > 0) {
                    $response = $this->rmaItems($rmaCollection);
                    $responseArray = $this->setResponse($response);
                } else {
                    $responseArray = $this->setResponse($response);
                }
            }else{
                $responseArray = $this->setResponse($response);
            }
        } catch (Exception $e) {
            
            $responseArray = [
                "responseCode" => Constant::RESPONSE_ERROR,
                "responseMessage" => $commonTraslateMessage['WebErrorMsg']
            ];
        }

        $this->logger->writeLog('============ END RMA ITEMS LIST API ==============');
        return json_encode($responseArray, JSON_UNESCAPED_UNICODE);
    }

    /**
     * list RMA details
     *
     * @param string $id
     * @return string
     */

    public function deleteRma($id) {

        try {
            
            $responseArray=[];
            $languageKey = $languageKey = $this->request->getParam('languageKey');
            $commonTraslateMessage = $this->traslateHelper->traslateMessages($languageKey);
            $this->logger->writeLog('============ RMA DELETE API ==============');
            $this->logger->writeLog('RMA ID: ' . $id);
            $authResult = $this->authnentication();
            if ($authResult !== null) {
                return json_encode($authResult, JSON_UNESCAPED_UNICODE);
            }
            
            $model = $this->rma->create();

            if($id){
                $rmaCollection = $this->rma->create()->getCollection();
                $rmaCollection->addFieldToFilter('entity_id', $id);
                if ($rmaCollection->getSize() > 0) {
                    $model->load($id);
                    $model->setData('rma_status',0);
                    $model->save();
                    $responseArray = [
                        "responseCode" => Constant::RESPONSE_SUCCSESS,
                        "responseMessage" => $commonTraslateMessage['rmaupdate']
                    ];
                }else{

                    $responseArray = [
                        "responseCode" => Constant::RESPONSE_WARNING,
                        "responseMessage" => $commonTraslateMessage['rmaidnotfound']
                    ]; 
                }
            }else{
                $responseArray = [
                    "responseCode" => Constant::RESPONSE_WARNING,
                    "responseMessage" => $commonTraslateMessage['rmaidnotfound']
                ];
            }

        } catch (Exception $e) {
            
            $responseArray = [
                "responseCode" => Constant::RESPONSE_ERROR,
                "responseMessage" => $commonTraslateMessage['WebErrorMsg']
            ];
        }

        $this->logger->writeLog('============ END RMA DELETE API ==============');
        return json_encode($responseArray, JSON_UNESCAPED_UNICODE);
    }


    /**
     * save Rma
     *
     * @param array $data
     * @return string
     */


    public function saveRma($data) {

        $languageKey = $this->traslateHelper->getlanguageKey($data);
        $commonTraslateMessage = $this->traslateHelper->traslateMessages($languageKey);
        $this->commonTraslatemsg = $commonTraslateMessage;

        try {

            $responseArray=[];
            $this->logger->writeLog('============ RMA SAVE API ==============');
            $jsonData = json_encode($data);
            $this->logger->writeLog('Request Data: ' . $jsonData);
            $authResult = $this->authnentication();
            if ($authResult !== null) {
                return json_encode($authResult, JSON_UNESCAPED_UNICODE);
            }
            $responseArray = $this->processSave($data);
        } catch (Exception $e) {
            $responseArray = [
                "responseCode" => Constant::RESPONSE_ERROR,
                "responseMessage" => $commonTraslateMessage['WebErrorSaveMsg']
            ];
        }

        $this->logger->writeLog('============ END RMA DELETE API ==============');
        return json_encode($responseArray, JSON_UNESCAPED_UNICODE);
    }

    /**
     * save Rma
     *
     * @param array $data
     * @return array
     */
    public function processSave($data) {

        $response = [];
        $model = $this->rma->create();
        $rmaItemsmodel = $this->itemFactory->create();
        $date = $this->date->date()->format('Y-m-d H:i:s');
                
        if($data['rmaId'] > 0){
            foreach ($data['items'] as $items) {
                $rmaitemsdata['rma_id'] = $data['rmaId'];
                $rmaitemsdata['product_id'] = $items['productId'];
                $rmaitemsdata['order_id'] = $items['orderRefNo'];
                $rmaitemsdata['qty'] = $items['qty'];
                $rmaitemsdata['reason'] = $items['reason'];
                $rmaitemsdata['support_media'] = $items['imagename'];
                $rmaitemsdata['status'] = "pending";
                $rmaitemsdata['remark'] = $items['remark'];
                
                if($items['rmaItemId'] > 0) {
                    $rmaItemsmodel->load($items['rmaItemId']);
                }else{
                    $rmaitemsdata['created_at'] = $date;
                }
                
                $rmaItemsmodel->addData($rmaitemsdata);
                $rmaItemsmodel->save();
                
                $response = [
                    "responseCode" => Constant::RESPONSE_SUCCSESS,
                    "responseMessage" => $this->commonTraslatemsg['rmaupdate']
                ]; 
            }
        }else{

            $rmadata['customer_id'] = $data['customerId'];
            $rmadata['salesrep_id'] = $data['salesRepId'];
            $rmadata['created_by'] = Constant::RMA_CREATED_BY;
            $rmadata['status'] = Constant::STATUS;
            $rmadata['rma_status'] = Constant::RMA_STATUS;
            $rmadata['created_at'] = $date;

            $model->setData($rmadata);
            $model->save();
            
            foreach ($data['items'] as $items) {
                
                $rmaitemsdata['rma_id'] = $model->getId();
                $rmaitemsdata['product_id'] = $items['productId'];
                $rmaitemsdata['qty'] = $items['qty'];
                $rmaitemsdata['order_id'] = $items['orderRefNo'];
                $rmaitemsdata['reason'] = $items['reason'];
                $rmaitemsdata['support_media'] = $items['imagename'];
                $rmaitemsdata['remark'] = $items['remark'];
                $rmaitemsdata['created_at'] = $date;
                $rmaitemsdata['status'] = Constant::RMA_ITEM_STATUS;
                $rmaItemsmodel->setData($rmaitemsdata);
                $rmaItemsmodel->save();
            }
            
            $response = [
                "responseCode" => Constant::RESPONSE_SUCCSESS,
                "responseMessage" => $this->commonTraslatemsg['rmsave']       
            ];
        }

        return $response;

    }

    /**
     * set validation response
     *
     * @return string
     */

    public function getAuthToken() {
        $token = false;

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == "HTTP_") {
                $headers[
                    str_replace(
                        " ",
                        "-",
                        ucwords(
                            strtolower(str_replace("_", " ", substr($name, 5)))
                        )
                    )
                ] = $value;
            }
        }
        $authorizationBearer = "";

        if (isset($headers["Authorization"])) {
            $authorizationBearer = $headers["Authorization"];
        } elseif (isset($headers["authorization"])) {
            $authorizationBearer = $headers["authorization"];
        } else {
            $authorizationBearer = "";
        }

        $authorizationBearerArr = explode(" ", $authorizationBearer);
        if (
            isset($authorizationBearerArr[0]) &&
            trim($authorizationBearerArr[0]) == "Bearer" &&
            isset($authorizationBearerArr[1])
        ) {
            $token = $authorizationBearerArr[1];
        }

        return $token;
    }


    /**
     * Rma List
     *
     * @param arry $collection
     * @return array
     */

    public function rmaListItems($collection)
    {
        $responsedata = [];
        
        foreach ($collection as $key => $itmes) {
            $status = $this->rmaStatus($itmes);
            $rmaCollection = $this->itemFactory->create()->getCollection();
            $rmaCollection->addFieldToFilter('rma_id', $itmes->getRmaId());
            $totalItem = $rmaCollection->getSize();

            $customerId = $itmes->getCustomerId();
            $salesrepId = $itmes->getSalesrepId();
            $customerData = $this->rmaitemsinfo->getCustomerData($customerId);
            $salesrepData = $this->rmaitemsinfo->getsalesRepData($salesrepId);
            
            $rmaListItem = [
                "rmaId" => (int)$itmes->getId(),
                "status" => $status ?? "",
                'rmaItemId' => $rmaCollection->getSize() > 0 ? $rmaCollection->getData()[0]['entity_id'] : '',
                "customerId" => $itmes->getCustomerId() ?? "",
                "customerName" =>  $customerData ? $customerData->getName() : '',
                "customerEmail" =>  $customerData ? $customerData->getEmail() : '',
                "salesRepId" => $salesrepData ? $salesrepData->getSalesrepresentativeId(): "",
                "salesRepName" => $salesrepData ? $salesrepData->getName() : '',
                "salesRepEmail" => $salesrepData ? $salesrepData->getEmail() : '',
                'totalItemcount'=> $totalItem,
                "createdDate" => $itmes->getCreatedAt() ? date("d/m/y", strtotime($itmes->getCreatedAt())) : "",
                "updatedDate" => $itmes->getUpdatedAt() ? date("d/m/y", strtotime($itmes->getUpdatedAt())) : "",
            ];
            
            $responsedata[] = $rmaListItem;
        }
        $this->logger->writeLog('====================== RESPONSE =========================');
        $this->logger->writeLog('Response : '.json_encode($responsedata,JSON_PRETTY_PRINT));
        $this->logger->writeLog('==========================================================');
        return $responsedata;
    }

    /**
     * Rma status
     *
     * @param arry $items
     * @return string
     */

    private function rmaStatus($items) {
        
        $status = "";

        if($items->getStatus() == (Constant::RMA_STATUS_PENDING)){
            $status = "Pending";
        }
        if($items->getStatus() == Constant::RMA_STATUS_FULLY_APPROVED){
            $status = "Fully Approved";
        }
        if($items->getStatus() == Constant::RMA_STATUS_REJECTED){
            $status = "Rejected";
        }
        if($items->getStatus() == Constant::RMA_STATUS_PARTIALLY_APPROVED){
            $status = "Partially Aprroved";
        }
        
        return $status;
    }

    /**
     * Rma Item List
     *
     * @param arry $collection
     * @return array
     */

    public function rmaItems($collection) {

        $responsedata = [];
        
        foreach ($collection as $key => $itmes) {

            $productRepo = $this->productRepository->getById($itmes->getProductId());
            $rmaListItem = [
                "rmaItemId" => (int)$itmes->getId(),
                "rmaId" => (int) $itmes->getRmaId(),
                "productId" => $itmes->getProductId() ?? "",
                "productName" => $productRepo ? $productRepo->getName() : "",
                "productSku" => $productRepo ? $productRepo->getSku() : "",
                "orderId" =>  $itmes->getOrderId() ?? '',
                "qty" =>  $itmes->getQty() ?? '',
                "reason" => $itmes->getReason() ?? "",
                "supportMedia" => $itmes->getsupportMedia() ?? '',
                'status' => $itmes->getStatus() ?? '',
                'remark' => $itmes->getRemark() ?? '',
                "createdDate" => $itmes->getCreatedAt() ? date("d/m/y", strtotime($itmes->getCreatedAt())) : "",
                "updatedDate" => $itmes->getUpdatedAt() ? date("d/m/y", strtotime($itmes->getUpdatedAt())) : "",
            ];
            
            $responsedata[] = $rmaListItem;
        }
        $this->logger->writeLog('====================== RESPONSE =========================');
        $this->logger->writeLog('Response : '.json_encode($responsedata,JSON_PRETTY_PRINT));
        $this->logger->writeLog('==========================================================');
        return $responsedata;
    }

    /**
     * set Response
     *
     * @param arry $response
     * @return array
     */

    public function setResponse($response)
    {
        if (!empty($response)) {
            $return = [
                "response" => [
                    "rma" => $response
                ],
                "responseCode" => Constant::RESPONSE_SUCCSESS,
                "responseMessage" => $this->commonTraslatemsg['WebSuccessMsg']
            ];
        } else {
            $return = [
                "responseCode" => Constant::RESPONSE_WARNING,
                "responseMessage" => $this->commonTraslatemsg['webRnf']
            ];
        }

        return $return;
    }

    /**
     * Upload Rma Image
     *
     * @return string
     */

    public function uploadRmaFile() {


        $languageKey = $this->request->getParam('languageKey');
        $commonTraslateMessage = $this->traslateHelper->traslateMessages($languageKey);
        
        try {

            $this->logger->writeLog('============ RMA SAVE API ==============');
            $fileInfo = $this->request->getFiles('images');
            
            $jsonData = json_encode($fileInfo);
            $this->logger->writeLog('Request Data: ' . $jsonData);
            $authResult = $this->authnentication();
            if ($authResult !== null) {
                return json_encode($authResult, JSON_UNESCAPED_UNICODE);
            }
            if (!$fileInfo) {
                throw new LocalizedException(__('No file uploaded.'));
            }
            
            $this->validateFiles($fileInfo);

            foreach ($fileInfo as $file) {
                $this->processFile($file);
            }
            
            $responseArray = [
                "responseCode" => Constant::RESPONSE_SUCCSESS,
                "responseMessage" => $commonTraslateMessage['fileuploadedsuccessfully']
            ];
            
        } catch (Exception $e) {
            $responseArray = [
                "responseCode" => Constant::RESPONSE_ERROR,
                "responseMessage" => $commonTraslateMessage['fileuploaderror']
            ];
        }

        $this->logger->writeLog('============ END UPLOAD IMAGE  API ==============');
        return json_encode($responseArray, JSON_UNESCAPED_UNICODE);
    }


    /**
     * @param array $fileInfo
     *
     * @throws ValidatorException
     */
    private function validateFiles(array $fileInfo)
    {
        if (!$fileInfo) {
            throw new ValidatorException(__('File info is not set'));
        }
        
        if (!is_array($fileInfo)) {
            throw new ValidatorException(__('File data should be an array'));
        }
        
        if (isset($fileInfo['error']) && $fileInfo['error']) {
            throw new ValidatorException(__('Unknown error'));
        }
        
        foreach ($fileInfo as $file) {
            $this->validateFile($file);
        }
    }


     /**
     * @param array $fileInfo
     *
     * @throws ValidatorException
     */

    public function validateFile($file) 
    {

        if (!isset($file['name'])) {
            throw new ValidatorException(__('File name is not set'));
        }
        
        if (!isset($file['type'])) {
            throw new ValidatorException(__('File type is not valid'));
        }
    }
    
    /**
     * @param array $fileInfo
     * @throws \Exception
     */
    private function processFile(array $fileInfo)
    {
        $uploader = $this->fileUploaderFactory->create(['fileId' => $fileInfo]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
        $fileAdapter = $this->adapterFactory->create();
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $destinationPath = $mediaDirectory->getAbsolutePath('customer/rma');
        $uploader->setFilesDispersion(false);
        $uploader->setAllowCreateFolders(true);
        $result = $uploader->save($destinationPath);
        return $result;
    }

    /**
     * BarrierToken Authorize
     *
     * @return string
     */

    public function authnentication() {

        $barrierToken = $this->getAuthToken();
        $integration = $this->_tokenModelFactory->create()->getCollection();
        $integration->addFieldToFilter('token', $barrierToken);

        return $integration->getSize() == 0
            ? [
                "responseCode" => Constant::RESPONSE_AUTH,
                "responseMessage" => $this->commonTraslatemsg['barrierTokenValidation']
            ]
            : null;
    }
}