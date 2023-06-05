<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Location extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('store_pickup_location', 'location_id');
    }
}
