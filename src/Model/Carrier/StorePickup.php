<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model\Carrier;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;

class StorePickup extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'store_pickup';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory
     */
    private \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory $collectionFactory;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private \Magento\Framework\App\Http\Context $httpContext;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->collectionFactory = $collectionFactory;
        $this->httpContext = $httpContext;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = $this->rateResultFactory->create();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter('store_id', $request->getStoreId());
        $collection->addFieldToFilter('customer_group_id', $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP));

        foreach ($collection as $location) {
            $rate = $this->rateMethodFactory->create([
                'data' => [
                    'carrier'       => $this->_code,
                    'carrier_title' => $this->getConfigData('title'),
                    'method'        => 'location_' . $location->getId(),
                    'method_title'  => $location->getLabel(),
                    'price'         => $location->getPrice(),
                    'cost'          => $location->getPrice(),
                ],
            ]);

            $result->append($rate);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [
            $this->_code => $this->getConfigData('name')
        ];
    }
}
