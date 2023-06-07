<?php

namespace MadeByMouses\StorePickup\Controller\Adminhtml\Location;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'MadeByMouses_StorePickup::manage_locations';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('MadeByMouses_StorePickup::manage_locations');

        $resultPage->getConfig()->getTitle()->prepend(__('Manage locations'));

        return $resultPage;
    }
}
