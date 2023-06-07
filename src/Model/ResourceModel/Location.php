<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Location extends AbstractDb
{
    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('store_pickup_location', 'location_id');
    }

    /**
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['spls' => $this->getTable('store_pickup_location_store')], 'store_id')
            ->join(
                ['spl' => $this->getMainTable()],
                'spls.location_id = spl.location_id',
                []
            )
            ->where('spl.location_id = :location_id');

        return $connection->fetchCol($select, ['location_id' => (int)$id]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getCustomerGroupIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['splcg' => $this->getTable('store_pickup_location_customer_group')], 'customer_group_id')
            ->join(
                ['spl' => $this->getMainTable()],
                'splcg.location_id = spl.location_id',
                []
            )
            ->where('spl.location_id = :location_id');

        return $connection->fetchCol($select, ['location_id' => (int)$id]);
    }

    /**
     * @inheritDoc
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
