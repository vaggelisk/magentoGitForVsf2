<?php

namespace Netsteps\Marketplace\Model\Product\Attribute;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;

class ChildNotVisibleInFront implements ChildNotVisibleInFrontInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $_productRepository;

    /**
     * @var \Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku
     */
    protected GetSourceItemBySourceCodeAndSku $_sourceItemBySourceCodeAndSku;

    /**
     * @var \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku
     */
    protected GetSourceItemsBySku $_sourceItemsBySku;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    protected SourceItemsSaveInterface $_sourceItemSave;

    /**
     * @var string[]
     */
    private array $attributesToUnset;

    /**
     * @var int
     */
    private int $thresholdToUserQuery;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected AttributeRepositoryInterface $_attributeRepository;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected \Magento\Framework\DB\Adapter\AdapterInterface $_resourceConnection;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku $sourceItemBySourceCodeAndSku
     * @param \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku $sourceItemsBySku
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSave
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param array $attributesToUnset
     * @param int $thresholdToUseQuery
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        GetSourceItemBySourceCodeAndSku $sourceItemBySourceCodeAndSku,
        GetSourceItemsBySku $sourceItemsBySku,
        SourceItemsSaveInterface $sourceItemsSave,
        AttributeRepositoryInterface $attributeRepository,
        ResourceConnection $resourceConnection,
        array $attributesToUnset = [],
        int $thresholdToUseQuery = 1000
    )
    {
        $this->_productRepository = $productRepository;
        $this->_sourceItemBySourceCodeAndSku = $sourceItemBySourceCodeAndSku;
        $this->_sourceItemsBySku = $sourceItemsBySku;
        $this->_sourceItemSave = $sourceItemsSave;
        $this->_attributeRepository = $attributeRepository;
        $this->_resourceConnection = $resourceConnection->getConnection();
        $this->attributesToUnset = $attributesToUnset;
        $this->thresholdToUserQuery = $thresholdToUseQuery;
    }

    /**
     * @inheritDoc
     */
    public function setChildAsNotVisibleInFront(array $products, string $distributorSource = 'all'): void
    {
        $sourceItems = [];
        $productIds = [];

        $productAttrProcessed = false;

        if(count($products) > $this->thresholdToUserQuery){
            $productIds = array_map([$this, 'exportProductId'], $products);
            $this->queryAttrTable($productIds);
            $productAttrProcessed = true;
        }

        foreach ($products as $product){

            if($sourceItems == 'all'){
                $items = $this->_sourceItemsBySku->execute($product->getSku());

                foreach ($items as $productSourceItem){
                    if($productSourceItem->getSourceCode() === 'default') continue;
                    $productSourceItem->setQuantity(0);
                    $productSourceItem->setStatus(Status::STATUS_OUT_OF_STOCK);
                    $sourceItems[] = $productSourceItem;
                }

                continue;
            }

            try{
                $sourceItem = $this->_sourceItemBySourceCodeAndSku->execute($distributorSource, $product->getSku());
                $sourceItem->setStatus(Status::STATUS_OUT_OF_STOCK);
                $sourceItem->setQuantity(0);
                $sourceItems[] = $sourceItem;
            }
            catch (\Throwable $e){
                continue;
            }

            if($productAttrProcessed){
                continue;
            }

            foreach ($this->attributesToUnset as $attributeToUnset){
                $product->unsetData($attributeToUnset);
            }

            $this->_productRepository->save($product);
        }

        if(empty($sourceItems)) return;

        $this->_sourceItemSave->execute($sourceItems);

        if(!empty($productIds)){
            $this->queryAttrTable($productIds);
        }
    }

    /**
     * @param array $productIds
     * @return void
     */
    private function queryAttrTable(array $productIds): void
    {

        $productIdsString = implode(',', $productIds);

        foreach ($this->attributesToUnset as $attributeCode){
            try{
                $attribute = $this->_attributeRepository->get(Product::ENTITY, $attributeCode);

                $where = sprintf("entity_id IN(%s) AND attribute_id = %d", $productIdsString, $attribute->getAttributeId());

                $this->_resourceConnection->delete($attribute->getBackendTable(), $where);
            }
            catch (\Throwable $e){
                continue;
            }
        }
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     */
    private function exportProductId(ProductInterface $product): int
    {
        return $product->getId();
    }
}
