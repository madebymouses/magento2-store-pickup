<?php

namespace MadeByMouses\StorePickup\Model\ResourceModel\Location\Relation\CustomerGroup;

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
        $oldCustomerGroups = $this->resourceModel->getCustomerGroupIds((int)$entity->getId());
        $newCustomerGroups = (array)$entity->getData('customer_group_ids');

        $connection = $this->resourceModel->getConnection();
        $table = $this->resourceModel->getTable('store_pickup_location_customer_group');

        $delete = array_diff($oldCustomerGroups, $newCustomerGroups);
        if ($delete) {
            $where = [
                'location_id = ?'          => (int)$entity->getData('location_id'),
                'customer_group_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newCustomerGroups, $oldCustomerGroups);
        if ($insert) {
            $data = [];
            foreach ($insert as $customerGroupId) {
                $data[] = [
                    'location_id'       => (int)$entity->getData('location_id'),
                    'customer_group_id' => (int)$customerGroupId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
