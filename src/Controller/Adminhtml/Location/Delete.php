<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Controller\Adminhtml\Location;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Delete extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'MadeByMouses_StorePickup::manage_locations';

    /**
     * @var \MadeByMouses\StorePickup\Api\LocationRepositoryInterface
     */
    private \MadeByMouses\StorePickup\Api\LocationRepositoryInterface $locationRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \MadeByMouses\StorePickup\Api\LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MadeByMouses\StorePickup\Api\LocationRepositoryInterface $locationRepository
    ) {
        parent::__construct($context);

        $this->locationRepository = $locationRepository;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $locationId = $this->getRequest()->getParam('location_id');
        if (!empty($locationId)) {
            try {
                $location = $this->locationRepository->get($locationId);
                $this->locationRepository->delete($location);

                $this->messageManager->addSuccessMessage(__('You deleted the location.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['location_id' => $locationId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
