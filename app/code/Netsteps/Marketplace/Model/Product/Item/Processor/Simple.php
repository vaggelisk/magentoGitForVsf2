<?php
/**
 * Simple
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Item\Processor;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Netsteps\Marketplace\Model\Feed\Action\SubAction\ReindexVsfBridge;
use Netsteps\Marketplace\Model\Feed\Action\SubActionManagerInterface;
use Netsteps\Marketplace\Model\Feed\Item;
use Netsteps\Marketplace\Model\Feed\ItemInterface;

/**
 * Class Simple
 * @package Netsteps\Marketplace\Model\Product\Item\Processor
 */
class Simple extends AbstractProcessor
{


    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function process(
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        SubActionManagerInterface $subActionManager
    ): void
    {

        $sku = $this->createSkuForItem($item, $this->getSkuParts($item));
        $this->normalizeItem($item);

        if ($this->_productHistoryRepository->isProductExists($sku)) {
            $product = $this->checkOrUpdateProductFromItem($sku, $item);
        } else {
            $product = $this->createProductFromItem($sku, $item);
            $product->setData('needs_reindex', true);
        }

        $needsReindex = false;
        if(!is_null($product) && $product->hasData('needs_reindex')){
            $needsReindex = $product->getData('needs_reindex');
        }

        if($needsReindex){
            $subActionManager->addActionItem(ReindexVsfBridge::ACTION_CODE, $product);
        }

        $this->_eventManager->dispatch(
            'marketplace_simple_processor_after',
            ['item' => $item, 'product' => $product, 'sub_action_manager' => $subActionManager]
        );
    }

    /**
     * Get data parts to create sku for product
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @return array
     */
    protected function getSkuParts(\Netsteps\Marketplace\Model\Feed\ItemInterface $item): array
    {
        return [];
    }

    /**
     * Create product from item
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createProductFromItem(string $sku, \Netsteps\Marketplace\Model\Feed\ItemInterface $item): \Magento\Catalog\Api\Data\ProductInterface
    {
        $exportedData = $this->_dataExporter->export($item, [\Netsteps\Marketplace\Model\Feed\ItemInterface::SKU]);

        /** @var  $item Item */
        $product = $this->createProduct();

        $product->setSku($sku)
            ->setStoreId(0)
            ->setTypeId('simple')
            ->setAttributeSetId($product->getDefaultAttributeSetId())
            ->setStatus(Status::STATUS_ENABLED)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setData('is_visible_in_front',1);

        $this->processAttributes($exportedData, $this->optionAttributes);
        $product->addData($exportedData);

        $this->processImages($product, $item);
        $this->assignCategories($product, $item);

        $product = $this->_productRepository->save($product);
        $this->_productHistoryRepository->createHistoryItem($sku, $this->_dataExporter->export($item));

        return $product;
    }

    /**
     * Check if product needs update and make the update
     *
     * @param string $sku
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @param array $additionalData
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function checkOrUpdateProductFromItem(
        string $sku,
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        array $additionalData = []
    ): \Magento\Catalog\Api\Data\ProductInterface
    {
        $data = $this->_dataExporter->export($item);

        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }

        $data['is_visible_in_front'] = ($item->getIsInStock() === ItemInterface::IN_STOCK_FLAG) ? 1 : 0;

        /**
         * If needed update then check if product has the flag
         * 'marketplace_ignore_update' after update then do not update version
         * table.
         */
        if ($this->_productHistoryRepository->isNeededUpdate($sku, $data)) {
            $product = $this->_productManagement->update($item, $sku, $additionalData);

            if (!$product->getData('marketplace_ignore_update')){
                $this->_productHistoryRepository->updateVersion($sku, $data);
            }
            return $product;
        }

        return $this->_productRepository->get($sku);
    }

    /**
     * Normalize product data
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @return void
     */
    protected function normalizeItem(\Netsteps\Marketplace\Model\Feed\ItemInterface $item): void
    {
        if (!$item->getWeight() || $item->getWeight() <= 0) {
            $item->setWeight(0.01);
        }

        if ($item->getSpecialPrice() && $item->getSpecialPrice() >= $item->getPrice()) {
            $item->setSpecialPrice(null);
        }
    }

    /**
     * Assign categories to product
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @return void
     */
    protected function assignCategories(
        \Magento\Catalog\Api\Data\ProductInterface     $product,
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item
    ): void
    {
        $categories = $item->getCategories();

        if (empty($categories)) {
            return;
        }

        $product->setCategoryIds($categories);
    }
}
