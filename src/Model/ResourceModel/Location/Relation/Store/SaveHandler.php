<?php

namespace MadeByMouses\StorePickup\Model\ResourceModel\Location\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\ResourceModel\Block;
use Magento\Framework\EntityManager\MetadataPool;

class SaveHandler implements ExtensionInterface
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
     *
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $oldStores = $this->resourceModel->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStores();

        $connection = $this->resourceModel->getConnection();
        $table = $this->resourceModel->getTable('store_pickup_location_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'location_id = ?' => (int)$entity->getData('location_id'),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'location_id' => (int)$entity->getData('location_id'),
                    'store_id'    => (int)$storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
