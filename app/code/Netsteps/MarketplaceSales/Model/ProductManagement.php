<?php
/**
 * ProductManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Framework\Serialize\SerializerInterface as Serializer;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface as ProductAttributeRepository;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Netsteps\Marketplace\Api\Data\MerchantDataInterface;
use Netsteps\MarketplaceSales\Api\ProductManagementInterface;
use Netsteps\Marketplace\Api\ProductIndexRepositoryInterface as ProductIndexRepository;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\Marketplace\Model\Product\Data\RepositoryInterface as MarketplaceProductDataRepository;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Psr\Log\LoggerInterface;

/**
 * Class ProductManagement
 * @package Netsteps\MarketplaceSales\Model
 */
class ProductManagement implements ProductManagementInterface
{
    /**
     * Attribute cached objects
     * @var AttributeInterface[]
     */
    private array $_attributeCache = [];

    /**
     * Cached data
     * @var array
     */
    private array $_cache = [];

    /**
     * @var ProductRepository
     */
    private ProductRepository $_productRepository;

    /**
     * @var ProductIndexRepository
     */
    private ProductIndexRepository $_productIndexRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var Serializer
     */
    private Serializer $_serializer;

    /**
     * @var ProductAttributeRepository
     */
    private ProductAttributeRepository $_productAttributeRepository;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @var MarketplaceProductDataRepository
     */
    private MarketplaceProductDataRepository $_marketplaceProductDataRepository;

    /**
     * @param ProductRepository $productRepository
     * @param ProductIndexRepository $productIndexRepository
     * @param ProductAttributeRepository $productAttributeRepository
     * @param ResourceConnection $resourceConnection
     * @param Serializer $serializer
     * @param EventManager $eventManager
     * @param MarketplaceProductDataRepository $marketplaceProductDataRepository
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        ProductRepository                $productRepository,
        ProductIndexRepository           $productIndexRepository,
        ProductAttributeRepository       $productAttributeRepository,
        ResourceConnection               $resourceConnection,
        Serializer                       $serializer,
        EventManager                     $eventManager,
        MarketplaceProductDataRepository $marketplaceProductDataRepository,
        LoggerPool                       $loggerPool
    )
    {
        $this->_productRepository = $productRepository;
        $this->_productIndexRepository = $productIndexRepository;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_connection = $resourceConnection->getConnection();
        $this->_serializer = $serializer;
        $this->_eventManager = $eventManager;
        $this->_marketplaceProductDataRepository = $marketplaceProductDataRepository;
        $this->_logger = $loggerPool->getLogger('debug');
    }

    /**
     * @inheritDoc
     */
    public function getLowestSellerId(int $productId, bool $force = false): ?int
    {
        $sellerData = $this->getLowestSellerData($productId, $force);
        return isset($sellerData['seller_id']) ? (int)$sellerData['seller_id'] : null;
    }

    /**
     * @inheritDoc
     */
    public function getLowestSellerData(int $productId, bool $force = false): array
    {
        if (array_key_exists($productId, $this->_cache) && !$force) {
            return $this->_cache[$productId];
        }

        $merchantData = $this->_productIndexRepository->getBestSellerDataByProductId($productId);
        $data = $merchantData ? $merchantData->toArray() : [];
        $this->_cache[$productId] = $data;

        return $data;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateSellerProductData(int $productId): void
    {
        $sellerData = $this->getLowestSellerData($productId);
        $sellerData = empty($sellerData) ? null : $this->_serializer->serialize($sellerData);
        $sellerId = $this->getLowestSellerId($productId);

        /** @var  $product \Magento\Catalog\Model\Product */
        $product = $this->_productRepository->getById($productId, true, 0);
        $product->addAttributeUpdate(self::LOWEST_SELLER_ID, $sellerId, 0);
        $product->addAttributeUpdate(self::LOWEST_SELLER_DATA, $sellerData, 0);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateProductsData(array $productIds = []): void
    {
        /** @var  $data MerchantDataInterface[] */
        foreach ($this->_productIndexRepository->getProductsBestOfferSeller($productIds) as $data) {
            /** Dispatch event before update seller data */
            $this->_eventManager->dispatch('mass_update_seller_data_before', ['data' => $data]);

            $this->_updateSellerData($data);

            /** Dispatch event after update seller data */
            $this->_eventManager->dispatch('mass_update_seller_data_after', ['data' => $data]);
        }
    }

    /**
     * Update merchant data
     * @param MerchantDataInterface[] $data
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _updateSellerData(array $data): void
    {
        /** @var  $lowestSellerAttribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $lowestSellerAttribute = $this->_getProductAttribute(self::LOWEST_SELLER_ID);
        /** @var  $lowestSellerDataAttribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $lowestSellerDataAttribute = $this->_getProductAttribute(self::LOWEST_SELLER_DATA);
        /** @var  $sellerDiscountAttribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $sellerDiscountAttribute = $this->_getProductAttribute(self::SELLER_DISCOUNT);

        $lowestSellerImports = [];
        $lowestSellerDataImports = [];
        $sellerDiscountImports = [];

        $productIds = array_map(function (\Netsteps\Marketplace\Model\Data\MerchantItemData $item) {
            return $item->getProductId();
        }, $data);

        $retailPriceMap = $this->_marketplaceProductDataRepository->getRetailPrices($productIds);

        /** @var  $merchantData \Netsteps\Marketplace\Model\Data\MerchantItemData */
        foreach ($data as $merchantData) {
            $isDeletion = $merchantData->getData('is_delete');
            $productId = $merchantData->getProductId();

            if($merchantData->getQuantity() == 0){
                $isDeletion = true;
            }

            $lowestSellerImports[] = $this->_createAttributeImportRecord(
                $lowestSellerAttribute, $productId, !$isDeletion ? $merchantData->getSellerId() : null
            );

            $lowestSellerDataImports[] = $this->_createAttributeImportRecord(
                $lowestSellerDataAttribute, $productId, !$isDeletion ? $merchantData->toJson() : null
            );

            $retailPrice = $retailPriceMap[$merchantData->getProductId()] ?? null;
            $sellerDiscountImports[] = $this->_createAttributeImportRecord(
                $sellerDiscountAttribute,
                $merchantData->getProductId(),
                !$isDeletion ? $this->getDiscount($merchantData->getFinalPrice(), $retailPrice) : null
            );
        }

        $this->_updateAttribute($lowestSellerAttribute, $lowestSellerImports);
        $this->_updateAttribute($lowestSellerDataAttribute, $lowestSellerDataImports);
        $this->_updateAttribute($sellerDiscountAttribute, $sellerDiscountImports);
        $this->_updateVisibility($productIds);
    }

    /**
     * Update custom visibility attribute
     * @param int[] $productIds
     * @return void
     */
    private function _updateVisibility(array $productIds): void
    {
        try {
            $visibilityAttribute = $this->_getProductAttribute(self::IS_VISIBLE_IN_FRONT);
            $visibleProductIds = $this->_getVisibleProductIds($productIds);
            $imports = $this->getVisibilityImports($visibleProductIds, $visibilityAttribute->getAttributeId());

            if (!empty($imports)) {
               $this->_updateAttribute($visibilityAttribute, $imports);
            }
        } catch (\Exception $e) {
            $this->_logger->error(
                __('Error on %1 method. Reason: %2', [__FUNCTION__, $e->getMessage()])
            );
        }
    }

    /**
     * Get visibility imports
     * @param array $ids
     * @param int $attributeId
     * @return array
     */
    private function getVisibilityImports(array $ids, int $attributeId): array
    {
        $select = $this->_connection->select()
            ->from(
                ['tmp' => $this->getQuantitySubSelect()],
                []
            )
            ->columns([
                'entity_id' => 'tmp.product_id',
                'value' => $this->_connection->getCheckSql('SUM(tmp.qty) > 0', 1, 0),
                'attribute_id' => new \Zend_Db_Expr($attributeId),
                'store_id' => new \Zend_Db_Expr(0)
            ])
            ->group('product_id')
            ->where('tmp.product_id IN (?)', $ids);

        return $this->_connection->fetchAll($select);
    }

    /**
     * Get quantity subselect
     * @return \Magento\Framework\DB\Select
     */
    private function getQuantitySubSelect(): \Magento\Framework\DB\Select
    {
        return $this->_connection->select()
            ->from(
                ['e' => $this->_connection->getTableName('catalog_product_entity')],
                []
            )
            ->joinLeft(
                ['isi' => $this->_connection->getTableName('inventory_source_item')],
                'e.sku = isi.sku',
                []
            )
            ->joinLeft(
                ['s' => $this->_connection->getTableName('catalog_product_super_link')],
                'e.entity_id = s.product_id',
                []
            )
            ->columns([
                'qty' => $this->_connection->getCheckSql('isi.status = 1 AND isi.source_code != \'default\'', 'isi.quantity', 0),
                'product_id' => $this->_connection->getCheckSql('s.parent_id IS NOT NULL', 's.parent_id', 'e.entity_id')
            ]);
    }

    /**
     * Get visible product ids from an array of product ids
     * @param int[] $productIds
     * @return int[]
     */
    private function _getVisibleProductIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $select = $this->_connection->select()
            ->from(
                ['e' => $this->_connection->getTableName('catalog_product_entity')],
                []
            )
            ->joinLeft(
                ['s' => $this->_connection->getTableName('catalog_product_super_link')],
                'e.entity_id = s.product_id',
                []
            )
            ->columns(
                ['product_id' => $this->_connection->getCheckSql('s.parent_id IS NOT NULL', 's.parent_id', 'e.entity_id')]
            )
            ->where('e.entity_id IN (?)', $productIds);

        return array_map(function (string $id) {
            return (int)$id;
        }, array_unique($this->_connection->fetchCol($select)));
    }

    /**
     * Get discount
     * @param float $finalPrice
     * @param float|null $initialPrice
     * @return int
     */
    private function getDiscount(float $finalPrice, ?float $initialPrice): int
    {
        if (!$finalPrice || !$initialPrice) {
            return 0;
        }

        return (int)round(100 - (100 * $finalPrice / $initialPrice));
    }

    /**
     * Update a single attribute with their values
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @param array $imports
     * @return int
     */
    private function _updateAttribute(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        array                                              $imports
    ): int
    {
        if (empty($imports)) {
            return 0;
        }

        try {
            return $this->_connection->insertOnDuplicate(
                $attribute->getBackendTable(),
                $imports,
                ['value']
            );
        } catch (\Exception $e) {
            $this->_logger->critical(
                __(
                    'Error on method %1 at class %1. Reason: %3',
                    [__FUNCTION__, get_class($this), $e->getMessage()]
                )
            );
            return 0;
        }
    }

    /**
     * Create attribute import record
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @param int $entityId
     * @param mixed $value
     * @return array
     */
    private function _createAttributeImportRecord(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        int                                                $entityId,
        mixed                                              $value
    ): array
    {
        return [
            'entity_id' => $entityId,
            'store_id' => 0,
            'attribute_id' => $attribute->getAttributeId(),
            'value' => $value
        ];
    }

    /**
     * Get product attribute by code
     * @param string $attributeCode
     * @return AttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _getProductAttribute(string $attributeCode): AttributeInterface
    {
        if (array_key_exists($attributeCode, $this->_attributeCache)) {
            return $this->_attributeCache[$attributeCode];
        }

        $attribute = $this->_productAttributeRepository->get($attributeCode);
        $this->_attributeCache[$attributeCode] = $attribute;
        return $attribute;
    }
}
