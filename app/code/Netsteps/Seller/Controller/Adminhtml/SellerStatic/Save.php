<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Controller\Adminhtml\SellerStatic;

use Magento\Framework\Exception\LocalizedException;
use Netsteps\Seller\Api\Data\SellerStaticInterface;
use Netsteps\Seller\Controller\Adminhtml\AbstractSeller;

class Save extends AbstractSeller
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            if (isset($data['general'])) {
                $data = $data['general'];
            }
            try {
            if ($id = (int)$this->getRequest()->getParam(SellerStaticInterface::ENTITY_ID)) {
                $model = $this->sellerStaticRepository->getById($id);
            } elseif (isset($data['entity_id']) && !empty($data['entity_id'])) {
                $model = $this->sellerStaticRepository->getById($data['entity_id']);
            } else {
                $model = $this->sellerStaticRepository->getEmptySellerStaticModel();
            }
            if(empty($data['entity_id'])){
                unset($data['entity_id']);
            }
            $model->addData($data);
            $model->setSelleryId($model->getSelleryId());

            $this->sellerStaticRepository->save($model);
            $this->messageManager->addSuccessMessage(__('Saved Successfully'));
            if ($this->getRequest()->getParam('back')) {
                return $this->_redirect('*/*/edit', [SellerStaticInterface::ENTITY_ID => $model->getEntityId()]);
            }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->_redirect('*/*/edit', [SellerStaticInterface::ENTITY_ID => $model->getEntityId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving seller static data. Please review the error log.')
                );
                return $this->_redirect('*/*/edit', [SellerStaticInterface::ENTITY_ID => $model->getEntityId()]);
            }
        }
        return $this->_redirect('*/*/grid');
    }


}
