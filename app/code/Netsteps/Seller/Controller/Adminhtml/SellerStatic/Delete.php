<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Controller\Adminhtml\SellerStatic;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerStaticInterface;
use Netsteps\Seller\Controller\Adminhtml\AbstractSeller;

class Delete extends AbstractSeller
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($id = (int)$this->getRequest()->getParam(SellerStaticInterface::ENTITY_ID)) {
            try {
                $model = $this->sellerStaticRepository->getById($id);
                $this->sellerStaticRepository->delete($model);
                $this->messageManager->addSuccessMessage($model->getSelleryId() . ' Seller Id has been deleted');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage('Seller Data Does not exists');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->_redirect('*/*/edit', [SellerStaticInterface::ENTITY_ID => $model->getEntityId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong. Please review the error log.')
                );
                $this->logger->critical($e);
                return $this->_redirect('*/*/edit', [SellerStaticInterface::ENTITY_ID => $model->getEntityId()]);
            }
        }
        return $this->_redirect('*/*/grid');
    }
}
