<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Api;

interface LocationRepositoryInterface
{

    /**
     * @param \MadeByMouses\StorePickup\Api\Data\LocationInterface $location
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \MadeByMouses\StorePickup\Api\Data\LocationInterface $location
    );

    /**
     * @param string $locationId
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($locationId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MadeByMouses\StorePickup\Api\Data\LocationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param \MadeByMouses\StorePickup\Api\Data\LocationInterface $location
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \MadeByMouses\StorePickup\Api\Data\LocationInterface $location
    );

    /**
     * @param string $locationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($locationId);
}
