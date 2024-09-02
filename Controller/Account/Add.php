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

namespace Magemonkey\RMA\Controller\Account;

class Add extends \Magento\Framework\App\Action\Action
{


    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var $notificationHelper
     */
    protected $notificationHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context,
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory,
     * @param \Magento\Framework\Message\ManagerInterface $messageManager,
     * @param \Magemonkey\RMA\Helper\Data $helper
     * @param \Magemonkey\RMA\Helper\EmailNotification $notificationHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magemonkey\RMA\Helper\Data $helper,
        \Magemonkey\RMA\Helper\EmailNotification $notificationHelper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->notificationHelper = $notificationHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        try {            
            
            $items = $this->getRequest()->getParams();           
            if(key_exists('rma_id', $items) || key_exists('product_id', $items)){
                $fileInfo = (array) $this->getRequest()->getFiles();                         
                $message ='';
                if($this->getRequest()->getParam('rma_id')) {
                $message = $this->helper->updateItems($items, $fileInfo);
                $this->notificationHelper->sendEmail($this->getRequest()->getParam('rma_id'));
                } else { 
                    $id = $this->helper->insertItmes($items, $fileInfo);
                    if($id) {                        
                      $data =   $this->notificationHelper->sendEmail($id);                      
                        //$message = "Return Request Generated";
                    }else {
                        $this->messageManager->addError("Something Went Wrong");                  
                    }            
                }
               
            } else {
                $message ="Please Add Product For Return";
                $this->messageManager->addError($message); 
            }            

        } catch (\Exception $e) {
            $this->messageManager->addError( $e->getMessage());      
        }      
        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return  $redirect->setUrl('/mm_rma/account/view/');
    }

}
