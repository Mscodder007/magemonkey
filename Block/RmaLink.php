<?php

namespace Magemonkey\RMA\Block;

class RmaLink extends \Magento\Framework\View\Element\Html\Link\Current
{

     /**
      * @var Session
      */
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
         $this->_customerSession = $customerSession;
         $this->customerFactory = $customerFactory;
         parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        $responseHtml = null;
        if ($this->_customerSession->isLoggedIn()) {

            $customer_id = $this->_customerSession->getId();
            $customerinfotype = $this->customerFactory->create()->load($customer_id);
            if ($customerinfotype->getCustomersTypes() == '1') {
                $responseHtml = parent::_toHtml();
            }
        }
        return $responseHtml;
    }
}
