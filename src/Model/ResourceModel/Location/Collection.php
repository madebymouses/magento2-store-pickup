<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model\ResourceModel\Location;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'location_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'store_pickup_location_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'location_collection';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \MadeByMouses\StorePickup\Model\Location::class,
            \MadeByMouses\StorePickup\Model\ResourceModel\Location::class
        );

        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['customer_group'] = 'customer_group_table.customer_group_id';
        $this->_map['fields']['location_id'] = 'main_table.location_id';
    }

    protected function _afterLoad()
    {
        $linkedIds = $this->getColumnValues($this->_idFieldName);

        if (count($linkedIds)) {
            $this->loadStores($linkedIds);
            $this->loadCustomerGroups($linkedIds);
        }

        return parent::_afterLoad();
    }

    private function loadStores(array $linkedIds)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(['store_pickup_location_store' => $this->getTable('store_pickup_location_store')])
            ->where('store_pickup_location_store.location_id IN (?)', $linkedIds);
        $result = $connection->fetchAll($select);

        if ($result) {
            $storesData = [];
            foreach ($result as $storeData) {
                $storesData[$storeData[$this->_idFieldName]][] = $storeData['store_id'];
            }

            foreach ($this as $item) {
                $linkedId = $item->getData($this->_idFieldName);
                if (!isset($storesData[$linkedId])) {
                    continue;
                }
                $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                if ($storeIdKey !== false) {
                    $stores = $this->storeManager->getStores(false, true);
                    $storeId = current($stores)->getId();
                    $storeCode = key($stores);
                } else {
                    $storeId = current($storesData[$linkedId]);
                    $storeCode = $this->storeManager->getStore($storeId)->getCode();
                }
                $item->setData('_first_store_id', $storeId);
                $item->setData('store_code', $storeCode);
                $item->setData('store_id', $storesData[$linkedId]);
            }
        }
    }

    private function loadCustomerGroups(array $linkedIds)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(['store_pickup_location_customer_group' => $this->getTable('store_pickup_location_customer_group')])
            ->where('store_pickup_location_customer_group.location_id IN (?)', $linkedIds);
        $result = $connection->fetchAll($select);

        if ($result) {
            $customerGroups = [];
            foreach ($result as $customerGroup) {
                $customerGroups[$customerGroup[$this->_idFieldName]][] = $customerGroup['customer_group_id'];
            }

            foreach ($this as $item) {
                $linkedId = $item->getData($this->_idFieldName);
                $item->setData('customer_group_ids', $customerGroups[$linkedId] ?? []);
            }
        }
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            if ($condition instanceof Store) {
                $store = [$condition->getId()];
            }

            if (!is_array($condition)) {
                $store = [$condition];
            }

            $store[] = Store::DEFAULT_STORE_ID;

            $this->addFilter('store', ['in' => $store], 'public');

            return $this;
        }

        if ($field === 'customer_group_id') {
            $this->addFilter('customer_group', $condition, 'public');

            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable('store_pickup_location_store')],
                'main_table.' . $this->_idFieldName . ' = store_table.' . $this->_idFieldName,
                []
            )->group(
                'main_table.' . $this->_idFieldName
            );
        }

        if ($this->getFilter('customer_group')) {
            $this->getSelect()->join(
                ['customer_group_table' => $this->getTable('store_pickup_location_customer_group')],
                'main_table.' . $this->_idFieldName . ' = store_table.' . $this->_idFieldName,
                []
            )->group(
                'main_table.' . $this->_idFieldName
            );
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }
}
