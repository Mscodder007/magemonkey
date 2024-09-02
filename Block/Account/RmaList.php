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

class RmaList extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magemonkey\RMA\Model\ResourceModel\Rma\CollectionFactory $rma,
        \Magemonkey\RMA\Model\RmaFactory $rmaFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->rma = $rma;
        $this->rmaFactory = $rmaFactory;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
    }


    /**
     * Get Rma List
     *
     * @return array $list;
     */
    public function getRmaCollection()
    {
        $customerId = $this->getCustomerDetails()->getCustomerId();
        $collection = $this->rma->create()->addFieldToFilter('customer_id',$customerId);
        return $collection;
    }

    public function getCustomerDetails()
    {
        return $this->currentCustomer;
    }

    public function getCustomerName()
    {
        return $fullName = $this->getCustomerDetails()->getCustomer()->getFirstName().' '.$this->getCustomerDetails()->getCustomer()->getLastName();
    }

    public function getStatus($status)
    {
        if($status == 0) {
            return 'Pending';
        } elseif($status == 1) {
            return "Approved";
        }elseif($status == 2) {
            return "Rejected";
        }elseif($status == 3) {
            return "Partially Aprroved";
        }else {
            return "Rejected";
        }
    }

}
