<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Controller\Adminhtml\Seller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerGroupInterface;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Controller\Adminhtml\AbstractSeller;
use Netsteps\Seller\Model\SellerRepository;

class Edit extends AbstractSeller
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $title = __('New Seller / Distributor');
        if ($id = (int)$this->getRequest()->getParam(SellerInterface::ENTITY_ID)) {
            try {
                $model = $this->sellerRepository->getById($id);
                $title = __('Edit Seller: %1', $model->getName());
                if($model->getGroup() === SellerGroupInterface::GROUP_DISTRIBUTOR){
                    $title = __('Edit Distributor: %1', $model->getName());
                }
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Entity no longer exists'));
                return $this->resultRedirectFactory->create()->setPath('*/*/index');
            }
        }
        else{
            $model = $this->sellerRepository->getEmptySellerModel();
        }
        $this->registry->register(SellerRepository::REGISTRY_KEY, $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Netsteps_Seller::sellers_grid');
        $resultPage->addBreadcrumb(__('Seller'), __('Seller'));
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
