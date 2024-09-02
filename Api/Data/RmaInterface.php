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

interface RmaInterface
{

    const RMA_ID = 'entity_id';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const CREATED_AT = 'created_at';
    const STATUS = 'status';
    const CUSTOMER_ID = 'customer_id';

    /**
     * Get rma_id
     * @return string|null
     */
    public function getRmaId();

    /**
     * Set rma_id
     * @param string $rmaId
     * @return \Magemonkey\RMA\Rma\Api\Data\RmaInterface
     */
    public function setRmaId($rmaId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Magemonkey\RMA\Rma\Api\Data\RmaInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get created_by
     * @return string|null
     */
    public function getCreatedBy();

    /**
     * Set created_by
     * @param string $createdBy
     * @return \Magemonkey\RMA\Rma\Api\Data\RmaInterface
     */
    public function setCreatedBy($createdBy);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Magemonkey\RMA\Rma\Api\Data\RmaInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Magemonkey\RMA\Rma\Api\Data\RmaInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Magemonkey\RMA\Rma\Api\Data\RmaInterface
     */
    public function setStatus($status);
}

