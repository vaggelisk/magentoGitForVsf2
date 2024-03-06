<?php
/**
 * Configurable
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Item\Processor;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Exception\InputException;
use Netsteps\Marketplace\Exception\InvalidValueException;
use Netsteps\Marketplace\Model\Feed\Action\SubAction\ReindexVsfBridge;
use Netsteps\Marketplace\Model\Feed\Action\SubActionManagerInterface;
use Netsteps\Marketplace\Model\Feed\Item;
use Netsteps\Marketplace\Model\Feed\ItemInterface;

/**
 * Class Configurable
 * @package Netsteps\Marketplace\Model\Product\Item\Processor
 */
class Configurable extends Simple
{
    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process(
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        SubActionManagerInterface $subActionManager
    ): void
    {
        /** @var $item \Netsteps\Marketplace\Model\Feed\Item */
        $sku = $this->createSkuForItem($item, $this->getSkuParts($item));
        $this->normalizeItem($item);
        $children = $this->processChildren($item, $sku, $subActionManager);
        $product = null;

        if (!$this->_productHistoryRepository->isProductExists($sku)) {
            $product = $this->createConfigurableProduct($sku, $item, $children);
            $product->setData('needs_reindex', true);
        } else if ($this->_productHistoryRepository->isNeededUpdate($sku, $this->_dataExporter->export($item))) {
            $product = $this->checkOrUpdateProductFromItem($sku, $item);
        } else if (!empty($children)) {
            $this->_productManagement->addConfigurableChildren($sku, $children);
            $product = $this->_productRepository->get($sku);
            $product->setData('needs_reindex', true);
        }

        $needsReindex = false;

        if($this->_productHistoryRepository->isProductExists($sku)){
            //Add images if product has empty gallery
            $product = clone $this->_productRepository->get($sku);
            $gallery = $product->getMediaGalleryEntries();
            if(empty($gallery)){
                $this->processImages($product, $item);
                $needsReindex = true;
            }
        }

        if(!is_null($product) && $product->hasData('needs_reindex')){
            $needsReindex = ($needsReindex || $product->getData('needs_reindex'));
        }

        if($needsReindex) {
            $subActionManager->addActionItem(ReindexVsfBridge::ACTION_CODE, $product);
        }

        $this->_eventManager->dispatch(
            'marketplace_configurable_processor_after',
            ['item' => $item, 'product' => $product, 'sub_action_manager' => $subActionManager]
        );
    }

    /**
     * Create configurable attribute options
     * @param \Magento\Catalog\Api\Data\ProductInterface $configurable
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $children
     * @return void
     * @throws InvalidValueException
     */
    private function linkProducts(
        \Magento\Catalog\Api\Data\ProductInterface $configurable,
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        array $children
    ): void {
        $attributeCodes = $item->getVariationAttributeCodes();

        if (!is_array($attributeCodes)) {
            throw new InvalidValueException(__('Missing variation attribute codes for product %1', $configurable->getSku()));
        }

        $extensionAttributes = $configurable->getExtensionAttributes();
        $extensionAttributes->setConfigurableProductOptions($this->createConfigurableProductOptions($attributeCodes, $children));
        $extensionAttributes->setConfigurableProductLinks(array_map(function ($product){
            return $product->getId();
        }, $children));
        $configurable->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Create configurable product options data during configurable product creation process
     * @param string[] $attributeCodes
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $children
     * @return array
     */
    private function createConfigurableProductOptions(array $attributeCodes, array $children): array {
        $options = [];

        foreach ($attributeCodes as $index => $attributeCode) {
            $values = [];
            $attribute = $this->_attributeManagement->getProductAttribute($attributeCode);

            foreach ($children as $child) {
                /** @var  $child \Magento\Catalog\Model\Product */
                if($value = $child->getData($attributeCode)) {
                  $values[$value] = $this->createConfigurableOptionValue()->setValueIndex($value);
                }
            }

            $option = $this->createConfigurableOption();
            $option->setAttributeId($attribute->getAttributeId())
                ->setLabel($attribute->getDefaultFrontendLabel())
                ->setPosition($index)
                ->setValues($values)
                ->setCode($attribute->getAttributeCode());

            $options[$attribute->getAttributeId()] = $option;
        }

        return $options;
    }

    /**
     * @param string $sku
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $children
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function createConfigurableProduct(
        string $sku,
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        array $children
    ): \Magento\Catalog\Api\Data\ProductInterface {
        if (count($children) === 0){
            $variationAttributes = $item->getVariationAttributeCodes() ?? [];
            $children = $this->loadChildren($sku, $variationAttributes);
        }

        if (count($children) === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Product %1 does not have children to assign', $sku)
            );
        }

        $configurableProduct = $this->createProduct();
        $configurableProduct
            ->setStatus(Status::STATUS_ENABLED)
            ->setTypeId(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            ->setAttributeSetId($configurableProduct->getDefaultAttributeSetId())
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStoreId(0)
            ->setSku($sku)
            ->setUrlKey($this->createUrlKey($item->getName(), $sku))
            ->setData('is_visible_in_front',1);
        ;

        $attributesData = $this->_dataExporter->export($item, [\Netsteps\Marketplace\Model\Feed\ItemInterface::SKU]);
        $this->processAttributes($attributesData, $this->optionAttributes);
        $configurableProduct->addData($attributesData);

        $this->processImages($configurableProduct, $item);
        $this->assignCategories($configurableProduct, $item);

        $this->linkProducts($configurableProduct, $item, $children);

        $configurableProduct = $this->_productRepository->save($configurableProduct);
        $this->_productHistoryRepository->createHistoryItem($configurableProduct->getSku(), $this->_dataExporter->export($item));

        return $configurableProduct;
    }

    /**
     * Load children by parent sku
     * @param string $sku
     * @param array $attributesToSelect
     * @return \Magento\Catalog\Model\Product[]
     */
    private function loadChildren(string $sku, array $attributesToSelect = []): array {
        $products = $this->createProductCollection();
        $products->addAttributeToFilter('type_id', 'simple')
            ->addAttributeToFilter('visibility', Visibility::VISIBILITY_NOT_VISIBLE)
            ->addAttributeToFilter('sku', ['like' => "{$sku}%"]);

        if (!empty($attributesToSelect)) {
            $products->addAttributeToSelect($attributesToSelect);
        }

        return $products->getItems();
    }

    /**
     * Process variation to create children
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @param string $parentSku
     * @param SubActionManagerInterface $subActionManager
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function processChildren(
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        string $parentSku,
        SubActionManagerInterface $subActionManager
    ): array
    {
        $children = [];

        $variations = $item->getVariations();

        $isCreateParent = $this->_productHistoryRepository->isProductExists($item->getSku());
        $isEmptyVariations = empty($variations);

        if($isCreateParent && $isEmptyVariations){
            throw new InputException(__("Cannot import product %1 with no variations", $item->getSku()));
        }
        elseif (!$isCreateParent && $isEmptyVariations){
            $product = clone $this->_productRepository->get($parentSku);
            $product->setData('is_visible_in_front', 0);
            $this->_productRepository->save($product);
        }

        foreach ($item->getVariations() as $variation) {
            $childItem = $this->createChildItem($item, $variation, $parentSku);

            if (!$childItem){
                continue;
            }

            $productData = $this->_dataExporter->export($childItem);

            if (!$this->_productHistoryRepository->isProductExists($childItem->getSku())){
                $product = $this->createSimpleProduct($childItem);
                $children[] = $product;
                $product->setData('needs_reindex', true);
                $this->_productHistoryRepository->createHistoryItem($childItem->getSku(), $productData);
            } else {
                $attributes = $childItem->getConfigurationAttributes() ?? [];
                $product = $this->checkOrUpdateProductFromItem($childItem->getSku(), $childItem, $attributes);
            }

            $needsReindex = false;
            if(!is_null($product) && $product->hasData('needs_reindex')){
                $needsReindex = $product->getData('needs_reindex');
            }

            if($needsReindex){
                $subActionManager->addActionItem(ReindexVsfBridge::ACTION_CODE, $product);
            }

            // This is to process both configurable and simple children on
            // updating the is_visible_in_front attribute
            $subActionManager->addActionItem(\Netsteps\Marketplace\Model\Feed\Action\SubAction\Status::ACTION_CODE, $childItem);

            $this->_eventManager->dispatch(
                'marketplace_simple_processor_after',
                ['item' => $childItem, 'product' => $product, 'sub_action_manager' => $subActionManager]
            );
        }

        return $children;
    }

    /**
     * Create simple product from item
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    private function createSimpleProduct(\Netsteps\Marketplace\Model\Feed\ItemInterface $item): \Magento\Catalog\Api\Data\ProductInterface {
        $product = $this->createProduct();
        $product->setStoreId(0)
            ->setStatus(Status::STATUS_ENABLED)
            ->setTypeId(Type::TYPE_SIMPLE)
            ->setAttributeSetId($product->getDefaultAttributeSetId())
            ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
            ->setUrlKey($this->createUrlKey($item->getName(), $item->getSku()))
            ->setData('is_visible_in_front',1);
        ;

        $productData = $this->_dataExporter->export($item);

        if ($attributes = $item->getConfigurationAttributes()){
            $productData = array_merge($productData, $attributes);
        }

        $this->processAttributes($productData, $this->optionAttributes);
        $product->addData($productData);

        /**
         * This line assigned the configurable categories to
         * child product. Commented out at 07/03/2023 because sometimes
         * child product created normally but an exception raised on configurable product
         * creation. Based on this we had a problem on category product count.
         */
//        $this->assignCategories($product, $item);

        return $this->_productRepository->save($product);
    }

    /**
     * Create child item
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $parent
     * @param Item\VariationInterface $variation
     * @param string $parentSku
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface|null
     */
    private function createChildItem(
        \Netsteps\Marketplace\Model\Feed\ItemInterface $parent,
        \Netsteps\Marketplace\Model\Feed\Item\VariationInterface $variation,
        string $parentSku
    ): ?\Netsteps\Marketplace\Model\Feed\ItemInterface {
        $attributes = $variation->getAttributes();

        if (empty($attributes)){
            return null;
        }

        ksort($attributes);

        $child = new Item();
        $child->setSku($parentSku);
        $child->setSku($this->createSkuForItem($child, array_values($attributes)));
        $child->setName($this->getChildName($parent->getName(), $attributes));
        $child->setBrand($parent->getBrand());
        $child->setSeason($parent->getSeason());
        $child->setDescription($parent->getDescription());
        $child->setStock($variation->getQty());
        $child->setIsInStock($this->getIsInStockForChild($child->getStock(), $parent->getIsInStock()));
        $child->setPrice($variation->getPrice() ?? $parent->getPrice());
        $child->setSpecialPrice($variation->getSpecialPrice() ?? $parent->getSpecialPrice());
        $child->setEstimatedDelivery($parent->getEstimatedDelivery());
        $child->setRetailPrice($parent->getRetailPrice());
        $child->setCategories(implode(',', $parent->getCategories()));
        $child->setWeight($parent->getWeight());
        $child->setEan($variation->getEan());
        $child->setNsDistributor($parent->getNsDistributor());
        $child->setConfigurationAttributes($attributes);
        $child->setStockSourceCode($parent->getStockSourceCode());

        if (!$child->hasData(ItemInterface::MPN)) {
            $child->setMpn($parentSku);
        }

        /** Possible this normalization is not needed because we normalize the parent item before */
        $this->normalizeItem($child);

        return $child;
    }

    /**
     * Get is in stock key for children
     * @param int $childQty
     * @param string $parentStock
     * @return string
     */
    private function getIsInStockForChild(int $childQty, string $parentStock): string {
        return $childQty > 0 && $parentStock === Item\StockMetadataInterface::IN_STOCK ?
            Item\StockMetadataInterface::IN_STOCK : Item\StockMetadataInterface::OUT_OF_STOCK;
    }

    /**
     * Get child name based on parent name and child attributes
     * @param string $parentName
     * @param array $attributes
     * @return string
     */
    protected function getChildName(string $parentName, array $attributes): string {
        $attributeValues = array_values($attributes);
        array_unshift($attributeValues, $parentName);
        return implode(' - ', $attributeValues);
    }
}
