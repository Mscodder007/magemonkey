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

namespace Magemonkey\RMA\Controller\Adminhtml\Rma;

use Magento\Framework\Exception\LocalizedException;
use Magemonkey\RMA\Model\RmaItemFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magemonkey\RMA\Model\RmaFactory;
use Magemonkey\RMA\Helper\EmailNotification;
use Magemonkey\RMA\Block\Adminhtml\Rma\Edit\Tab\View\PersonalInfo;

class Updateitem extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        RmaItemFactory $rmaItemFactory,
        JsonFactory $resultJsonFactory,
        RmaFactory $rmaFactory,
        EmailNotification $emailNotification,
        PersonalInfo $rmaitemsinfo
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->rmaFactory = $rmaFactory;
        $this->emailNotification = $emailNotification;
        $this->rmaitemsinfo = $rmaitemsinfo;
        parent::__construct($context);
    }

    /**
     * Updateitem action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //exit('test');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $model = $this->rmaItemFactory->create();
        $msg = '';
        
        $allApproved = true;
        $allRejected = true;
        $allPending = true;
        
        try {
            if($data['rma_item_id']) {
                $model->load($data['rma_item_id']);
                $model->setData('remark',$data['remark']);
                $model->setData('status',$data['status']);
                $model->save();
            }
            
            $rmaItemCollection = $this->rmaItemFactory->create()->getCollection();
            $rmaItemCollection->addFieldToFilter('rma_id', $model->getData('rma_id'));

            foreach ($rmaItemCollection as $rmaItem) {
                
                $itemStatus = $rmaItem->getData('status');
                
                if ($itemStatus !== 'approved') {
                    $allApproved = false;
                }

                if ($itemStatus !== 'rejected') {
                    $allRejected = false;
                }
                if ($itemStatus !== 'pending') {
                    $allPending = false;
                }
            }
            
            $rmaModel = $this->rmaFactory->create();
            $rmaModel->load($data['rma_id']);
            $rmastatus;
            if ($allApproved) {
                $rmastatus = 1;
            } elseif ($allRejected) {
                $rmastatus = 2;
            } elseif ($allPending) {
                $rmastatus = 0;
            } else {
               $rmastatus = 3;
            }
            $customerId = $rmaModel->getCustomerId();
            $salesrepId = $rmaModel->getSalesrepId();
            $customerData = $this->rmaitemsinfo->getCustomerData($customerId);
            $salesrepData = $this->rmaitemsinfo->getsalesRepData($salesrepId);
            
            if($customerData){
                
                $customerEmail = $customerData->getEmail();
                $customerName = $customerData->getName();
                
                if($customerEmail != null && $customerEmail != '') {
                    $this->emailNotification->emailRMAItemNotification($customerEmail, $customerName);
                }
            }
            if($salesrepData){
                
                $salesRepEmail = $salesrepData->getEmail();
                $salesRepName = $salesrepData->getName();
                
                if($salesRepEmail != null && $salesRepEmail != '') {
                    $this->emailNotification->emailRMAItemNotification($salesRepEmail, $salesRepName);
                }
            }
            
            $rmaModel->setData('status',$rmastatus);
            $rmaModel->save();

            $msg = 'RMA Item Update Successfully.';
            
        } catch (Exception $e) {
            $msg = 'something went wrong save data.';
        }
        return $result->setData(['message'=>$msg]);
    }
}

