<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Controller\Adminhtml\Seller;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerFeedInterface;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Controller\Adminhtml\AbstractSeller;

class Save extends AbstractSeller
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            if (isset($data['seller'])) {
                $data = $data['seller'];
            }
            try {
                if ($id = (int)$this->getRequest()->getParam(SellerInterface::ENTITY_ID)) {
                    $model = $this->sellerRepository->getById($id);
                } elseif (isset($data['general']['entity_id']) && !empty($data['general']['entity_id'])) {
                    $model = $this->sellerRepository->getById($data['general']['entity_id']);
                } else {
                    $model = $this->sellerRepository->getEmptySellerModel();
                }

                $this->attachOptionsToSeller($data, $model);
                $this->attachFeedsToSeller($data, $model);

                $model->addData($data['general']);
                $model = $this->sellerRepository->save($model);

                if (!isset($data['general']['entity_id']) && $model->getEntityId()) {
                    $data['general']['entity_id'] = $model->getEntityId();
                }
                $this->saveStaticData($data);

                $this->messageManager->addSuccessMessage(__('Saved Successfully'));
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', [SellerInterface::ENTITY_ID => $model->getId()]);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->_redirect('*/*/edit', [SellerInterface::ENTITY_ID => $model->getId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving seller data. Please review the error log.')
                );
                $this->logger->critical($e);
                return $this->_redirect('*/*/edit', [SellerInterface::ENTITY_ID => $model->getId()]);
            }
        }
        return $this->_redirect('*/*/grid');
    }

    /**
     * @param array $data
     * @param SellerInterface $seller
     * @return SellerInterface
     */
    private function attachOptionsToSeller(array $data, SellerInterface $seller): SellerInterface
    {
        $sellerOptions = [];
        $logoValue = false;
        if ($seller->getEntityId()) {
            $sellerOptions = $this->sellerOptionRepository->getBySellerId($seller->getEntityId());
        }
        if (empty($sellerOptions)) {
            foreach ($data['options'] as $key => $value) {
                $instance = $this->sellerOptionFactory->create();
                $instance->setOptionName($key);
                $instance->setOptionValue($value);
                $sellerOptions[] = $instance;
            }
            $seller->setOptions($sellerOptions);
            return $seller;
        } else {
            foreach ($sellerOptions as $option) {
                foreach ($data['options'] as $key => $value) {
                    if ($option->getOptionName() === $key) {
                        $option->setOptionValue($value);
                    }

                    if($key == 'logo' && is_array($value) && !empty($value)) {
                        if(isset($value[0]['url'])) {
                            $logoValue = $value[0]['url'];
                        }
                    }
                }
            }
            $seller->setOptions($sellerOptions);
        }

        if($logoValue) {
            $optionCollection = $this->sellerOptionCollection;
            $optionCollection->addFieldToFilter('seller_id', ['eq' => $seller->getEntityId()]);
            $optionCollection->addFieldToFilter('option_name', ['eq' => 'logo']);
            if(!count($optionCollection)) {
                $sellerOption = $this->sellerOptionFactory->create();
                $storeManager = $this->storeManagerInterface;
                $sellerOption->setSellerId($seller->getEntityId());
                $sellerOption->setOptionName('logo');
                $sellerOption->setOptionValue($logoValue);
                $sellerOption->setStoreId($storeManager->getStore()->getId());
                $sellerOption->save();
            }
        }
        return $seller;
    }

    /**
     * @param array $data
     * @param SellerInterface $seller
     * @return SellerInterface
     */
    private function attachFeedsToSeller(array $data, SellerInterface $seller): SellerInterface
    {
        $feeds = [];
        if ($seller->getEntityId()) {
            $feeds = $this->sellerFeedRepository->getBySellerId($seller->getEntityId());
        }
        if (empty($feeds)) {
            foreach ($data['feeds'] as $key => $value) {
                if (!empty($value)) {
                    /** @var SellerFeedInterface $instance */
                    $instance = $this->sellerFeedInterfaceFactory->create();
                    $instance->setType($key);
                    $instance->setUrlPath($value);
                    $feeds[] = $instance;
                }
            }
            $seller->setFeeds($feeds);
            return $seller;
        } else {
            foreach ($feeds as $feed) {
                foreach ($data['feeds'] as $key => $value) {
                    if ($feed->getType() === $key) {
                        $feed->setUrlPath($value);
                        unset($data['feeds'][$key]);
                    }
                }
            }
            if (!empty($data['feeds'])) {
                foreach ($data['feeds'] as $key => $value) {
                    if (!empty($value)) {
                        $instance = $this->sellerFeedInterfaceFactory->create();
                        $instance->setType($key);
                        $instance->setUrlPath($value);
                        $feeds[] = $instance;
                    }
                }
            }
            $seller->setFeeds($feeds);
        }

        return $seller;
    }

    /**
     * @param array $data
     * @return void
     */
    private function saveStaticData(array $data): void
    {
        if (!isset($data['general']['entity_id'])) {
            return;
        }
        $staticObject = $this->sellerStaticFactory->create();
        try {
            $storedData = $this->sellerStaticRepository->getBySellerId((int)$data['general']['entity_id']);
            $staticObject->setEntityId($storedData->getEntityId());
        } catch (NoSuchEntityException $e) {

        }
        $staticObject->addData($data['static']);
        $staticObject->setSelleryId($data['general']['entity_id']);
        $this->sellerStaticRepository->save($staticObject);
    }

}
