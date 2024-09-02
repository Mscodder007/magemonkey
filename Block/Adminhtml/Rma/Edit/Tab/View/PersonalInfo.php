<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magemonkey\RMA\Block\Adminhtml\Rma\Edit\Tab\View;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Adminhtml customer view personal information sales block.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PersonalInfo extends \Magento\Backend\Block\Template
{
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var RmaItemFactory
     */
    protected $itemFactory;

    /**
     * @var RmaFactory
     */
    protected $rma;


    /**

     * @param \Magento\Customer\Model $customer
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magemonkey\RMA\Model\RmaItemFactory $itemFactory,
        \Magemonkey\RMA\Model\RmaFactory $rma,
        \Magemonkey\SalesRepresentative\Model\SalesRepresentativeFactory $salesRepresentativeFactory,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->itemFactory = $itemFactory;
        $this->salesRepFactory = $salesRepresentativeFactory;
        $this->rma = $rma;
        parent::__construct($context, $data);
    }

    public function getRmaData()
    {
       // echo '<pre>';print_r($this->getRequest()->getParam('entity_id'));die('dead');
        try {
            $id = $this->getRequest()->getParam('entity_id');
            $item = $this->rma->create()->load($id);
            return $item;
        } catch (\Exception $e) {
             return $e->getMessage();
        }
    }

    public function getCustomerData($customerId)
    {

        try {
            if($customerId) {
                $customerFactory = $this->customer->create();
                $customerItems = $customerFactory->load($customerId);
                return $customerItems;
            }
        } catch (\Exception $e) {
             return $e->getMessage();
        }
    }

    public function getsalesRepData($salesrepId)
    {
        try {
            if($salesrepId) {
                $salesRep = $this->salesRepFactory->create();
                $salesRepItems = $salesRep->load($salesrepId);
                return $salesRepItems;
            }
        } catch (\Exception $e) {
             return $e->getMessage();
        }
    }
}
