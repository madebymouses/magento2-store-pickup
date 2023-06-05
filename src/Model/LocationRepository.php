<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model;

use MadeByMouses\StorePickup\Api\Data\LocationInterface;
use MadeByMouses\StorePickup\Api\Data\LocationInterfaceFactory;
use MadeByMouses\StorePickup\Api\Data\LocationSearchResultsInterfaceFactory;
use MadeByMouses\StorePickup\Api\LocationRepositoryInterface;
use MadeByMouses\StorePickup\Model\ResourceModel\Location as ResourceLocation;
use MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var \MadeByMouses\StorePickup\Model\ResourceModel\Location
     */
    protected $resource;

    /**
     * @var \MadeByMouses\StorePickup\Api\Data\LocationInterfaceFactory
     */
    protected $locationFactory;

    /**
     * @var \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * @var \MadeByMouses\StorePickup\Api\Data\LocationSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @param \MadeByMouses\StorePickup\Model\ResourceModel\Location $resource
     * @param \MadeByMouses\StorePickup\Api\Data\LocationInterfaceFactory $locationFactory
     * @param \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory $locationCollectionFactory
     * @param \MadeByMouses\StorePickup\Api\Data\LocationSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceLocation $resource,
        LocationInterfaceFactory $locationFactory,
        LocationCollectionFactory $locationCollectionFactory,
        LocationSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->locationFactory = $locationFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(LocationInterface $location)
    {
        try {
            $this->resource->save($location);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the location: %1',
                $exception->getMessage()
            ));
        }
        return $location;
    }

    /**
     * @inheritDoc
     */
    public function get($locationId)
    {
        $location = $this->locationFactory->create();
        $this->resource->load($location, $locationId);
        if (!$location->getId()) {
            throw new NoSuchEntityException(__('Location with id "%1" does not exist.', $locationId));
        }
        return $location;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->locationCollectionFactory->create();

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
    public function delete(LocationInterface $location)
    {
        try {
            $locationModel = $this->locationFactory->create();
            $this->resource->load($locationModel, $location->getLocationId());
            $this->resource->delete($locationModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Location: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($locationId)
    {
        return $this->delete($this->get($locationId));
    }
}
