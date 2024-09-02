<?php
/**
 * Copyright © RMA All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magemonkey\RMA\Api\Data;

interface RmaItemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get RmaItem list.
     * @return \Magemonkey\RMA\Api\Data\RmaItemInterface[]
     */
    public function getItems();

    /**
     * Set rma_id list.
     * @param \Magemonkey\RMA\Api\Data\RmaItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

