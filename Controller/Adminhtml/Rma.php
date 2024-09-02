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

namespace Magemonkey\RMA\Controller\Adminhtml;

abstract class Rma extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'Magemonkey_RMA::top_level';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Magemonkey'), __('Magemonkey'))
            ->addBreadcrumb(__('Rma'), __('Rma'));
        return $resultPage;
    }
}

