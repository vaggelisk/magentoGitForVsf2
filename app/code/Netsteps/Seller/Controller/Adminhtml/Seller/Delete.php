<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Controller\Adminhtml\Seller;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Controller\Adminhtml\AbstractSeller;

class Delete extends AbstractSeller
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($id = (int)$this->getRequest()->getParam(SellerInterface::ENTITY_ID)) {
            try {
                $model = $this->sellerRepository->getById($id);
                $this->sellerRepository->delete($model);
                $this->messageManager->addSuccessMessage($model->getName() . ' has been deleted');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage('Seller Does not exists');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->_redirect('*/*/edit', [SellerInterface::ENTITY_ID => $model->getId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong. Please review the error log.')
                );
                $this->logger->critical($e);
                return $this->_redirect('*/*/edit', [SellerInterface::ENTITY_ID => $model->getId()]);
            }
        }
        return $this->_redirect('*/*/grid');
    }
}
