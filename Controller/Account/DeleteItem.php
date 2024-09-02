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

class DeleteItem extends \Magento\Framework\App\Action\Action
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
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context,
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory,
     * @param \Magento\Framework\Message\ManagerInterface $messageManager,
     * @param \Magemonkey\RMA\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magemonkey\RMA\Helper\Data $helper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $this->helper->deleteItem($id);
        } catch (\Exception $e) {
             $e->getMessage();
        }
        $this->messageManager->addSuccess('Item was removed from then list');
        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return  $redirect->setUrl($this->_redirect->getRefererUrl());
    }

}
