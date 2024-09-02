<?php

namespace Magemonkey\RMA\Block\Adminhtml\Rma;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magemonkey\RMA\Model\RmaItemRepository;
use Magemonkey\RMA\Model\ResourceModel\RmaItem\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;

class RmaItems extends \Magento\Framework\View\Element\Template
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'tab/view/rma_items.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        RmaItemRepository $rmaItemRepository,
        CollectionFactory $collectionFactory,
        ProductRepository $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->rmaItemRepository = $rmaItemRepository;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
    }
        
    /**
     * get Base Credit limit
     *
     * @return mixed
     */
    public function getRmaItemscollection()
    {   
        $rmaId = $this->getRequest()->getParam('entity_id');
        if($rmaId){
            // $filters[] = $this->filterBuilder->setField('rma_id')->setConditionType('eq')->setValue($rmaId)->create();
            // $this->searchCriteriaBuilder->addFilters($filters);
            // $searchCriteria = $this->searchCriteriaBuilder->create();
            // $searchResults = $this->rmaItemRepository->getList($searchCriteria);
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('rma_id', $rmaId);
            return  $collection;
        }
    }

    /**
     * get product Info
     *
     * @return mixed
     */

    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
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
                    $allImages[] = $this->getBaseUrl().'/media/customer/rma/'.$image;
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
