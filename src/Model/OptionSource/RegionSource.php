<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model\OptionSource;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class RegionSource implements OptionSourceInterface
{
    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * Source data
     *
     * @var null|array
     */
    private $sourceData;

    /**
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     */
    public function __construct(CollectionFactory $regionCollectionFactory)
    {
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (null === $this->sourceData) {
            $regionCollection = $this->regionCollectionFactory->create();
            $this->sourceData = $regionCollection->toOptionArray();
        }
        return $this->sourceData;
    }
}
