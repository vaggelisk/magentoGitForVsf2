<?php
/**
 * Management
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Item;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;
use Netsteps\Marketplace\Model\Data\MerchantItemData;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Product\AttributeManagementInterface as AttributeManagement;
use Netsteps\Marketplace\Model\Product\Item\Processor\Context;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Magento\Framework\Stdlib\ArrayManager as ArrayManager;
use Netsteps\Marketplace\Model\Data\ExporterInterface as DataExporter;
use Netsteps\Marketplace\Traits\AttributeProcessorTrait;
use Psr\Log\LoggerInterface;


use Netsteps\Marketplace\Model\Processor\Product\MerchantProcessorInterface as MerchantProcessor;

/**
 * Class Management
 * @package Netsteps\Marketplace\Model\Product\Item
 */
class Management implements ManagementInterface
{
    use AttributeProcessorTrait;

    /**
     * @var DataExporter
     */
    private DataExporter $_dataExporter;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $_productRepository;

    /**
     * @var ArrayManager
     */
    private ArrayManager $_arrayManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var AttributeManagement
     */
    private AttributeManagement $_attributeManagement;

    /**
     * @var LinkManagementInterface
     */
    private LinkManagementInterface $_linkManagement;

    /**
     * @var MerchantProcessor
     */
    private MerchantProcessor $_merchantProcessor;

    /**
     * @param Context $context
     * @param AttributeManagement $attributeManagement
     * @param ArrayManager $arrayManager
     * @param DataExporter $dataExporter
     * @param MerchantProcessor $merchantProcessor
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        Context                $context,
        AttributeManagement    $attributeManagement,
        ArrayManager           $arrayManager,
        DataExporter           $dataExporter,
        MerchantProcessor      $merchantProcessor,
        LoggerPool             $loggerPool
    )
    {
        $this->_productRepository = $context->getProductRepository();
        $this->_linkManagement = $context->getLinkManagement();
        $this->_attributeManagement = $attributeManagement;
        $this->_arrayManager = $arrayManager;
        $this->_dataExporter = $dataExporter;
        $this->_merchantProcessor = $merchantProcessor;
        $this->_logger = $loggerPool->getLogger('debug');
    }

    /**
     * @inheritDoc
     */
    public function update(
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        string $sku,
        array $additionalData = [],
        int $storeId = 0
    ): \Magento\Catalog\Api\Data\ProductInterface
    {
        /** @var  $product \Magento\Catalog\Model\Product */
        $product = $this->_productRepository->get($sku, true, $storeId, true);
        $product->setStatus(Status::STATUS_ENABLED);

        $isInStock = ($item->getIsInStock() === ItemInterface::IN_STOCK_FLAG) ? 1 : 0;

        /**
         * If item has distributor and item distributor is different from the already set distributor
         */
        if ($item->getNsDistributor() && $product->getNsDistributor() && ((int)$product->getNsDistributor() !== (int)$item->getNsDistributor())) {
            if (in_array($product->getTypeId(), MerchantItemData::getAvailableProductTypesToIndex())) {
                $this->_merchantProcessor->processItem($item, $item->getNsDistributor(), $product->getId());
            }
            $product->setData('marketplace_ignore_update', true);
            return $product;
        }

        /** Prepare new data with custom handling for categories */
        $newData = $this->_dataExporter->export($item);

        if (!empty($additionalData)) {
            $newData = array_merge($newData, $additionalData);
        }

        $this->processItemCategories($newData);
        $this->processAttributes($newData, ['brand', 'season', 'size', 'length', 'color']);
        ksort($newData);

        /** Prepare old data with custom handling for categories */
        $oldData = $product->toArray(array_keys($newData));
        ksort($oldData);

        $deltas = array_udiff_assoc($newData, $oldData, [$this, 'compareValues']);

        $deltas[ItemInterface::DISTRIBUTOR_ID] = $item->getNsDistributor();

        if($product->getData('is_visible_in_front') !== $isInStock){
            $deltas['is_visible_in_front'] = $isInStock;
        }

        if (empty($deltas)) {
            return $product;
        }

        if(
            !empty($deltas[ItemInterface::DISTRIBUTOR_ID]) &&
            $product->getNsDistributor() === $deltas[ItemInterface::DISTRIBUTOR_ID]
        )
        {
            return $product;
        }

        $product->addData($deltas);

        if(
            array_key_exists(ItemInterface::STOCK, $deltas) ||
            array_key_exists(ItemInterface::IS_IN_STOCK, $deltas)
        )
        {
            $product->setData('needs_reindex', true);
        }

        if (isset($deltas['category_ids'])) {
            $categoryLinks = $product->getExtensionAttributes()->getCategoryLinks();

            if (is_array($categoryLinks)) {
                $categoryIds = $product->getCategoryIds();
                foreach ($categoryLinks as $index => $categoryLink) {
                    if (!in_array($categoryLink->getCategoryId(), $categoryIds)) {
                        unset($categoryLinks[$index]);
                    }
                }

                $extensionAttributes = $product->getExtensionAttributes();
                $extensionAttributes->setCategoryLinks(array_values($categoryLinks));
                $product->setExtensionAttributes($extensionAttributes);
            }
        }

        return $this->_productRepository->save($product);
    }

    /**
     * @inheritDoc
     */
    protected function getAttributeManagement(): \Netsteps\Marketplace\Model\Product\AttributeManagementInterface
    {
        return $this->_attributeManagement;
    }

    /**
     * Compare values callback
     * @param mixed $value1
     * @param mixed $value2
     * @return bool
     */
    private function compareValues(mixed $value1, mixed $value2): bool
    {
        if (is_numeric($value1)) {
            $value1 = (float)$value1;
        }

        if (is_numeric($value2)) {
            $value2 = (float)$value2;
        }

        return $value1 !== $value2;
    }

    /**
     * @inheritDoc
     */
    public function addConfigurableChildren(string $configurableSku, array $children): void
    {
        foreach ($children as $child) {
            $this->_linkManagement->addChild($configurableSku, $child->getSku());
        }
    }
}
