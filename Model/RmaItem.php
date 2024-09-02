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

namespace Magemonkey\RMA\Model;

use Magemonkey\RMA\Api\Data\RmaItemInterface;
use Magento\Framework\Model\AbstractModel;

class RmaItem extends AbstractModel implements RmaItemInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Magemonkey\RMA\Model\ResourceModel\RmaItem::class);
    }

    /**
     * @inheritDoc
     */
    public function getRmaitemId()
    {
        return $this->getData(self::RMAITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRmaitemId($rmaitemId)
    {
        return $this->setData(self::RMAITEM_ID, $rmaitemId);
    }

    /**
     * @inheritDoc
     */
    public function getRmaId()
    {
        return $this->getData(self::RMA_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRmaId($rmaId)
    {
        return $this->setData(self::RMA_ID, $rmaId);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getReason()
    {
        return $this->getData(self::REASON);
    }

    /**
     * @inheritDoc
     */
    public function setReason($reason)
    {
        return $this->setData(self::REASON, $reason);
    }

    /**
     * @inheritDoc
     */
    public function getSupportMedia()
    {
        return $this->getData(self::SUPPORT_MEDIA);
    }

    /**
     * @inheritDoc
     */
    public function setSupportMedia($supportMedia)
    {
        return $this->setData(self::SUPPORT_MEDIA, $supportMedia);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

