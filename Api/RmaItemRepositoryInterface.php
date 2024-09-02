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

namespace Magemonkey\RMA\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RmaItemRepositoryInterface
{

    /**
     * Save RmaItem
     * @param \Magemonkey\RMA\Api\Data\RmaItemInterface $rmaItem
     * @return \Magemonkey\RMA\Api\Data\RmaItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magemonkey\RMA\Api\Data\RmaItemInterface $rmaItem
    );

    /**
     * Retrieve RmaItem
     * @param string $rmaitemId
     * @return \Magemonkey\RMA\Api\Data\RmaItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($rmaitemId);

    /**
     * Retrieve RmaItem matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magemonkey\RMA\Api\Data\RmaItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete RmaItem
     * @param \Magemonkey\RMA\Api\Data\RmaItemInterface $rmaItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Magemonkey\RMA\Api\Data\RmaItemInterface $rmaItem
    );

    /**
     * Delete RmaItem by ID
     * @param string $rmaitemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($rmaitemId);
}

