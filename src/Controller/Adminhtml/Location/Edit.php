<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Controller\Adminhtml\Location;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
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
     * New action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $locationId = $this->getRequest()->getParam('location_id');
        if (!empty($locationId)) {
            try {
                $location = $this->locationRepository->get($locationId);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(__('This location no longer exists.'));

                return $this->resultRedirectFactory
                    ->create()
                    ->setPath('*/*/');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('MadeByMouses_StorePickup::manage_locations');
        $resultPage->getConfig()->getTitle()->prepend($locationId ? __('Edit location') : ('Add new location'));

        return $resultPage;
    }
}
