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

namespace Magemonkey\RMA\Block\Account;

use Magento\Store\Model\ScopeInterface;

class Index extends \Magento\Framework\View\Element\Template
{

    const REASONS = 'rma/rma_status/rma_reasons';
    const RMASTATUS = 'rma/rma_status/rma_product_status';

    const KEY = 'rma/general/rma_api_token';
   /**
    * Constructor
    *
    * @param \Magento\Framework\View\Element\Template\Context $context
    * @param \Magemonkey\RMA\Model\ResourceModel\RmaItem\CollectionFactory $rmaItems
    * @param \Magento\Catalog\Model\ProductFactory $productFactory
    * @param \Magento\Framework\Serialize\Serializer\Json $serialize
    * @param \Magento\Store\Model\ScopeInterface $scopeConfig
    * @param array $data
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magemonkey\RMA\Model\ResourceModel\RmaItem\CollectionFactory $rmaItems,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        array $data = []
    ) {
        $this->rmaItems = $rmaItems;
        $this->productFactory = $productFactory;
        $this->serialize = $serialize;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        parent::__construct($context, $data);
    }

    /**
     * List RMA Items
     *
     * @return void
     */
    public function getRmaItems()
    {
        try {
            if($this->getRequest()->getParam('id')) {
                $collection = $this->rmaItems->create();
                $items = $collection->addFieldToFilter('rma_id',$this->getRequest()->getParam('id'));
                return $items;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }

    /**
     * Get Product Factory
     *
     * @return object
     */
    public function getProductDetails()
    {
        return $this->productFactory->create();
    }

    /**
     * Get Product Details
     *
     * @param int $id
     * @return string
     */
    public function getImage($id)
    {
        try {
            if($id) {
                $allImages = [];
                $productFactory =  $this->getProductDetails();
                $product = $productFactory->load($id);
                $images = $product->getMediaGalleryImages();
                foreach ($images as $image) {
                    if (isset($image['url']) && $image['url'] !== '') {
                        return  $image->getUrl();
                    }
                 }
            } else {
                return false;
            }
        } catch (\Exception $e) {
           return  $e->getMessage();
        }
    }

    /**
     * Get Product Name
     *
     * @param int $id
     * @return string $name
     */
    public function getItemName($id)
    {
        try {
            if($id) {
                $allImages = [];
                $productFactory =  $this->getProductDetails();
                $product = $productFactory->load($id);
                $name = $product->getName();
                return $name;
            } else {
                return false;
            }
        } catch (\Exception $e) {
           return  $e->getMessage();
        }
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
            if($url) {
                $allImages = [];
                $imagesUrl = explode(",", $url);
                foreach ($imagesUrl as  $image) {
                    $allImages[] = $this->getUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'customer/rma/'.$image;
                }
                return $allImages;
            } else {
                return false;
            }
        } catch (\Exception $e) {
           return  $e->getMessage();
        }
    }

    public function getReasons()
    {
        $reasonConfig = $this->scopeConfig->getValue(self::REASONS,ScopeInterface::SCOPE_STORE,$this->getStoreid());
        if($reasonConfig == '' || $reasonConfig == null){
            return;
        }
        $unserializedata = $this->serialize->unserialize($reasonConfig);
        $reasonArray = array();
        foreach($unserializedata as $key => $row)
        {
            if(($row['required']) == 1 && ($row['enable']) == 1){
                $reasonArray[] = $row['label'];
            }
        }
        return $reasonArray;
    }

    public function getRmaStatus()
    {
        $statusConfig = $this->scopeConfig->getValue(self::RMASTATUS,ScopeInterface::SCOPE_STORE,$this->getStoreid());
        if($statusConfig == '' || $statusConfig == null){
            return;
        }
        $unserializedata = $this->serialize->unserialize($statusConfig);
        $statusArray = array();
        foreach($unserializedata as $key => $row)
        {
            if(($row['required']) == 1 && ($row['enable']) == 1){
                $statusArray[] = $row['label'];
            }
        }
        return $statusArray;
    }

    public function getToken()
    {
        $secret = $this->scopeConfig->getValue(self::KEY,ScopeInterface::SCOPE_STORE,$this->getStoreid());
        $secret = $this->encryptor->decrypt($secret);
        return $secret;

    }

}
