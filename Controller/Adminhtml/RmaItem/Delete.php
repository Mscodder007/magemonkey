<?php
/**
 * Copyright © RMA All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magemonkey\RMA\Controller\Adminhtml\RmaItem;

class Delete extends \Magemonkey\RMA\Controller\Adminhtml\RmaItem
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('rmaitem_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Magemonkey\RMA\Model\RmaItem::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Rmaitem.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['rmaitem_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Rmaitem to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

