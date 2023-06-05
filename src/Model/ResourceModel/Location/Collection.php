<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model\ResourceModel\Location;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'location_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \MadeByMouses\StorePickup\Model\Location::class,
            \MadeByMouses\StorePickup\Model\ResourceModel\Location::class
        );
    }
}
