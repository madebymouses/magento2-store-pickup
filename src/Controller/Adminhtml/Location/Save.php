<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Controller\Adminhtml\Location;

use MadeByMouses\StorePickup\Api\LocationRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'MadeByMouses_StorePickup::manage_locations';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \MadeByMouses\StorePickup\Api\LocationRepositoryInterface
     */
    private LocationRepositoryInterface $locationRepository;

    /**
     * @var \MadeByMouses\StorePickup\Model\LocationFactory
     */
    private \MadeByMouses\StorePickup\Model\LocationFactory $locationFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \MadeByMouses\StorePickup\Api\LocationRepositoryInterface $locationRepository
     * @param \MadeByMouses\StorePickup\Model\LocationFactory $locationFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \MadeByMouses\StorePickup\Api\LocationRepositoryInterface $locationRepository,
        \MadeByMouses\StorePickup\Model\LocationFactory $locationFactory
    ) {
        parent::__construct($context);

        $this->dataPersistor = $dataPersistor;
        $this->locationRepository = $locationRepository;
        $this->locationFactory = $locationFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $locationId = $this->getRequest()->getParam('location_id');

            if (!empty($locationId)) {
                try {
                    $location = $this->locationRepository->get($locationId);
                } catch (LocalizedException $exception) {
                    $this->messageManager->addErrorMessage(__('This location no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $location = $this->locationFactory->create();
            }

            $location->setData($data);

            try {
                $this->locationRepository->save($location);
                $this->messageManager->addSuccessMessage(__('You saved the location.'));
                $this->dataPersistor->clear('store_pickup_location');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['location_id' => $location->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the location.'));
            }

            $this->dataPersistor->set('store_pickup_location', $data);
            return $resultRedirect->setPath('*/*/edit', ['location_id' => $this->getRequest()->getParam('location_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
