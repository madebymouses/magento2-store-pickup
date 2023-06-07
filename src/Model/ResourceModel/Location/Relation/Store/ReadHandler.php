<?php

namespace MadeByMouses\StorePickup\Model\ResourceModel\Location\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var \MadeByMouses\StorePickup\Model\ResourceModel\Location
     */
    protected $resourceModel;

    /**
     * @param \MadeByMouses\StorePickup\Model\ResourceModel\Location $resourceBlock
     */
    public function __construct(
        \MadeByMouses\StorePickup\Model\ResourceModel\Location $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resourceModel->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}
