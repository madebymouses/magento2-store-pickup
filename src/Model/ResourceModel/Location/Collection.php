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
        $this->_map['fields']['location_id'] = 'main_table.location_id';
    }

    protected function _afterLoad()
    {
        $linkedIds = $this->getColumnValues($this->_idFieldName);

        if (count($linkedIds)) {
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

        return parent::_afterLoad();
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

        parent::_renderFiltersBefore();
    }
}
