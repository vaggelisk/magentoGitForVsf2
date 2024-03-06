<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Controller\Adminhtml\SellerStatic;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Controller\Adminhtml\AbstractSeller;
use Netsteps\Seller\Model\SellerStaticRepository;

class Edit extends AbstractSeller
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $title = __('Add Seller Static Data');
        if ($id = (int)$this->getRequest()->getParam(SellerInterface::ENTITY_ID)) {
            try {
                $model = $this->sellerStaticRepository->getById($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Entity no longer exists'));
                return $this->resultRedirectFactory->create()->setPath('*/*/index');
            }
        }
        else{
            $model = $this->sellerStaticRepository->getEmptySellerStaticModel();
        }
        $this->registry->register(SellerStaticRepository::REGISTRY_KEY, $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Netsteps_Seller::sellers_static_grid');
        $resultPage->addBreadcrumb(__('Seller'), __('Seller Static'));
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
