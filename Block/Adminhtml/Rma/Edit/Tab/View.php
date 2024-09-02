<?php

namespace Magemonkey\RMA\Block\Adminhtml\Rma\Edit\Tab;

use Magento\Ui\Component\Layout\Tabs\TabInterface;

Class View extends \Magento\Backend\Block\Template implements TabInterface

{
   /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    // /**
    //  * @return string|null
    //  */
    // public function getCustomerId()
    // {
    //     return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    // }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Rma View');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Rma View');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {

        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        // if ($this->getCustomerId()) {
        //     return false;
        // }
        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
