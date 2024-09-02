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

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__("RMA List"));
            return $resultPage;
    }
}

