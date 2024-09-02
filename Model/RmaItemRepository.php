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
use Magemonkey\RMA\Api\Data\RmaItemInterfaceFactory;
use Magemonkey\RMA\Api\Data\RmaItemSearchResultsInterfaceFactory;
use Magemonkey\RMA\Api\RmaItemRepositoryInterface;
use Magemonkey\RMA\Model\ResourceModel\RmaItem as ResourceRmaItem;
use Magemonkey\RMA\Model\ResourceModel\RmaItem\CollectionFactory as RmaItemCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class RmaItemRepository implements RmaItemRepositoryInterface
{

    /**
     * @var ResourceRmaItem
     */
    protected $resource;

    /**
     * @var RmaItemCollectionFactory
     */
    protected $rmaItemCollectionFactory;

    /**
     * @var RmaItem
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var RmaItemInterfaceFactory
     */
    protected $rmaItemFactory;


    /**
     * @param ResourceRmaItem $resource
     * @param RmaItemInterfaceFactory $rmaItemFactory
     * @param RmaItemCollectionFactory $rmaItemCollectionFactory
     * @param RmaItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceRmaItem $resource,
        RmaItemInterfaceFactory $rmaItemFactory,
        RmaItemCollectionFactory $rmaItemCollectionFactory,
        RmaItemSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->rmaItemCollectionFactory = $rmaItemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(RmaItemInterface $rmaItem)
    {
        try {
            $this->resource->save($rmaItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rmaItem: %1',
                $exception->getMessage()
            ));
        }
        return $rmaItem;
    }

    /**
     * @inheritDoc
     */
    public function get($rmaItemId)
    {
        $rmaItem = $this->rmaItemFactory->create();
        $this->resource->load($rmaItem, $rmaItemId);
        if (!$rmaItem->getId()) {
            throw new NoSuchEntityException(__('RmaItem with id "%1" does not exist.', $rmaItemId));
        }
        return $rmaItem;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->rmaItemCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(RmaItemInterface $rmaItem)
    {
        try {
            $rmaItemModel = $this->rmaItemFactory->create();
            $this->resource->load($rmaItemModel, $rmaItem->getRmaitemId());
            $this->resource->delete($rmaItemModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the RmaItem: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($rmaItemId)
    {
        return $this->delete($this->get($rmaItemId));
    }
}

