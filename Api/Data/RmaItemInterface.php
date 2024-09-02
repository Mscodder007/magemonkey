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

namespace Magemonkey\RMA\Api\Data;

interface RmaItemInterface
{

    const SUPPORT_MEDIA = 'support_media';
    const QTY = 'qty';
    const RMA_ID = 'rma_id';
    const ORDER_ID = 'order_id';
    const REASON = 'reason';
    const RMAITEM_ID = 'entity_id';
    const STATUS = 'status';
    const PRODUCT_ID = 'product_id';

    /**
     * Get rmaitem_id
     * @return string|null
     */
    public function getRmaitemId();

    /**
     * Set entity_id
     * @param string $rmaitemId
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setRmaitemId($rmaitemId);

    /**
     * Get rma_id
     * @return string|null
     */
    public function getRmaId();

    /**
     * Set rma_id
     * @param string $rmaId
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setRmaId($rmaId);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setProductId($productId);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setOrderId($orderId);

    /**
     * Get qty
     * @return string|null
     */
    public function getQty();

    /**
     * Set qty
     * @param string $qty
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setQty($qty);

    /**
     * Get reason
     * @return string|null
     */
    public function getReason();

    /**
     * Set reason
     * @param string $reason
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setReason($reason);

    /**
     * Get support_media
     * @return string|null
     */
    public function getSupportMedia();

    /**
     * Set support_media
     * @param string $supportMedia
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setSupportMedia($supportMedia);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Magemonkey\RMA\RmaItem\Api\Data\RmaItemInterface
     */
    public function setStatus($status);
}

