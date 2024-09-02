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

use Magemonkey\RMA\Api\Data\RmaInterface;
use Magemonkey\RMA\Api\Data\RmaInterfaceFactory;
use Magemonkey\RMA\Api\Data\RmaSearchResultsInterfaceFactory;
use Magemonkey\RMA\Api\RmaRepositoryInterface;
use Magemonkey\RMA\Model\ResourceModel\Rma as ResourceRma;
use Magemonkey\RMA\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class RmaRepository implements RmaRepositoryInterface
{

    /**
     * @var ResourceRma
     */
    protected $resource;

    /**
     * @var RmaInterfaceFactory
     */
    protected $rmaFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Rma
     */
    protected $searchResultsFactory;

    /**
     * @var RmaCollectionFactory
     */
    protected $rmaCollectionFactory;


    /**
     * @param ResourceRma $resource
     * @param RmaInterfaceFactory $rmaFactory
     * @param RmaCollectionFactory $rmaCollectionFactory
     * @param RmaSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceRma $resource,
        RmaInterfaceFactory $rmaFactory,
        RmaCollectionFactory $rmaCollectionFactory,
        RmaSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->rmaFactory = $rmaFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(RmaInterface $rma)
    {
        try {
            $this->resource->save($rma);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rma: %1',
                $exception->getMessage()
            ));
        }
        return $rma;
    }

    /**
     * @inheritDoc
     */
    public function get($rmaId)
    {
        $rma = $this->rmaFactory->create();
        $this->resource->load($rma, $rmaId);
        if (!$rma->getId()) {
            throw new NoSuchEntityException(__('Rma with id "%1" does not exist.', $rmaId));
        }
        return $rma;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->rmaCollectionFactory->create();

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
    public function delete(RmaInterface $rma)
    {
        try {
            $rmaModel = $this->rmaFactory->create();
            $this->resource->load($rmaModel, $rma->getRmaId());
            $this->resource->delete($rmaModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Rma: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($rmaId)
    {
        return $this->delete($this->get($rmaId));
    }
}

