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
use Magento\Store\Model\StoreManagerInterface;

class GetRmaItemImage extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
    public $storeManager;

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
        PersonalInfo $rmaitemsinfo,
        StoreManagerInterface $storeManager
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->rmaFactory = $rmaFactory;
        $this->emailNotification = $emailNotification;
        $this->rmaitemsinfo = $rmaitemsinfo;
        $this->storeManager=$storeManager;
        parent::__construct($context);
    }

    /**
     * Updateitem action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $collection = $this->rmaItemFactory->create()->getCollection();
        $msg = '';
        $html = '<div class="container">';
        try {
            $collection->addFieldToFilter('entity_id',$data['rma_item_id']);
            foreach($collection as $item){
                if (!empty($item->getSupportMedia())){
                    $images = $this->getSupportMedia($item->getSupportMedia());
                    foreach ($images as $image){
                     $html .= '<div class="item"><div class="fancyWrapDiv"><a data-fancybox="Image" class="rma-image-view" href="'.$image.'" data-caption=""><div class="catalog-img-wrapper img ">
                        <img class="last" src='.$image.'></div></a></div></div>';           
                    }
                }
            }
            $html .= "</div>";
            $msg = 'Get Rma Image';
            
        } catch (Exception $e) {
            $msg = 'something went wrong save data.';
        }
        return $result->setData(['message'=>$msg,'html'=>$html]);
    }


    /**
     * Get Support Media For Rma Item
     *
     * @param string $url
     * $return array $imageUrl
     */
    public function getSupportMedia($url)
    {
        try {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'customer/rma/';
            if($url) {
                $allImages = [];
                $imagesUrl = explode(",", $url);
                foreach ($imagesUrl as  $image) {
                    $allImages[] = $baseUrl.$image;
                }
                return $allImages;
            } else {
                return false;
            }
        } catch (\Exception $e) {
           return  $e->getMessage();
        }
    }
}

