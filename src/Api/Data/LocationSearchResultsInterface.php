<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Api\Data;

interface LocationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * @param \MadeByMouses\StorePickup\Api\Data\LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
