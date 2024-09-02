<?php
/**
 * Copyright © RMA All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magemonkey\RMA\Api\Data;

interface RmaSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Rma list.
     * @return \Magemonkey\RMA\Api\Data\RmaInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Magemonkey\RMA\Api\Data\RmaInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

