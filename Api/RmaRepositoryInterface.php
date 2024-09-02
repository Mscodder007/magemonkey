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

interface RmaRepositoryInterface
{

    /**
     * Save Rma
     * @param \Magemonkey\RMA\Api\Data\RmaInterface $rma
     * @return \Magemonkey\RMA\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magemonkey\RMA\Api\Data\RmaInterface $rma
    );

    /**
     * Retrieve Rma
     * @param string $rmaId
     * @return \Magemonkey\RMA\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($rmaId);

    /**
     * Retrieve Rma matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magemonkey\RMA\Api\Data\RmaSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Rma
     * @param \Magemonkey\RMA\Api\Data\RmaInterface $rma
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Magemonkey\RMA\Api\Data\RmaInterface $rma
    );

    /**
     * Delete Rma by ID
     * @param string $rmaId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($rmaId);
}

