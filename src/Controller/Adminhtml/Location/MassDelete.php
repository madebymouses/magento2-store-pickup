<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Controller\Adminhtml\Location;

use MadeByMouses\StorePickup\Api\LocationRepositoryInterface;
use MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private Filter $filter;

    /**
     * @var \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var \MadeByMouses\StorePickup\Api\LocationRepositoryInterface
     */
    private LocationRepositoryInterface $locationRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \MadeByMouses\StorePickup\Model\ResourceModel\Location\CollectionFactory $collectionFactory
     * @param \MadeByMouses\StorePickup\Api\LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LocationRepositoryInterface $locationRepository
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $location) {
            $this->locationRepository->delete($location);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
