<?php
/**
 * Merchant
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Processor\Product;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Netsteps\Marketplace\Api\StockManagementInterface;
use Netsteps\Marketplace\Model\Feed\Item\StockMetadataInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Processor\ResultInterface;
use Netsteps\Marketplace\Model\Product\Item\Processor\Context;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;
use Netsteps\Marketplace\Model\Processor\ResultInterfaceFactory as ResultFactory;
use Netsteps\Marketplace\Api\Data\MerchantDataInterface as MerchantData;
use Netsteps\Marketplace\Model\Data\MerchantItemData;

/**
 * Class Merchant
 * @package Netsteps\Marketplace\Model\Processor\Product
 */
class MerchantProcessor implements MerchantProcessorInterface
{
    /**
     * @var StockManagementInterface
     */
    private StockManagementInterface $_stockManagement;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @var ResultFactory
     */
    private ResultFactory $_resultFactory;

    /**
     * Feed's stock data
     * @var array
     */
    private array $_stockData = [];

    /**
     * Feed's pricing data
     * @var array
     */
    private array $_priceData = [];

    /**
     * @var ResultInterface|null
     */
    private ?ResultInterface $result = null;

    /**
     * @var SellerInterface|null
     */
    private ?SellerInterface $seller = null;

    /**
     * @param Context $context
     * @param SellerRepository $sellerRepository
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context          $context,
        SellerRepository $sellerRepository,
        ResultFactory    $resultFactory
    )
    {
        $this->_stockManagement = $context->getStockManagement();
        $this->_connection = $context->getConnection();
        $this->_sellerRepository = $sellerRepository;
        $this->_resultFactory = $resultFactory;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processItems(array $items, int $sellerId): ResultInterface
    {
        $this->clearData();
        $this->processData($items, $sellerId);
        $this->updatePrices();
        $this->updateStock();
        return $this->result;
    }

    /**
     * @return void
     */
    private function clearData(): void
    {
        $this->_stockData = [];
        $this->_priceData = [];
        $this->seller = null;
        $this->result = $this->_resultFactory->create();
    }

    /**
     * Process data to normalize them
     * @param ItemInterface[] $items
     * @param int $sellerId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function processData(array $items, int $sellerId): void
    {
        $errors = [];
        $this->seller = $this->_sellerRepository->getById($sellerId);
        $productMap = $this->_getProductMap(array_keys($items));

        foreach ($items as $item) {
            $sku = $item->getSku();

            if (!array_key_exists($sku, $productMap)) {
                $errors[] = __('Product %1 does not exist or has invalid product type', $sku);
                continue;
            }

            $productId = (int)$productMap[$sku];

            $this->_priceData[$productId] = [
                MerchantData::PRODUCT_ID => $productId,
                MerchantData::SELLER_ID => $this->seller->getEntityId(),
                MerchantData::PRICE => $item->getPrice(),
                MerchantData::SPECIAL_PRICE => $item->getSpecialPrice(),
                MerchantData::DELIVERY_ID => $item->getEstimatedDelivery(),
                MerchantData::EAN => $item->getEan(),
            ];


            $this->_stockData[$item->getSku()] = $this->getStockItem($item, $this->seller->getSourceCode());
        }

        $this->result->setErrors($errors)
            ->setProcessedData($this->_priceData)
            ->setAdditionalData($this->_stockData);
    }

    /**
     * Get the source item for a specific feed item
     * @param ItemInterface $item
     * @param string $sourceCode
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface
     */
    private function getStockItem(ItemInterface $item, string $sourceCode): \Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        $isInStock = $item->getStock() > 0 && $item->getIsInStock() === StockMetadataInterface::IN_STOCK;

        return $this->_stockManagement->createSourceItem(
            $item->getSku(),
            max(0, $item->getStock()),
            $isInStock,
            $sourceCode
        );
    }

    /**
     * Update prices
     * @return void
     */
    private function updatePrices(): void
    {
        $productIds = [];

        if (!empty($this->_priceData)) {
            $productIds = array_keys($this->_priceData);

            $this->_connection->insertOnDuplicate(
                MerchantData::TABLE,
                $this->_priceData,
                [MerchantData::PRICE, MerchantData::SPECIAL_PRICE, MerchantData::DELIVERY_ID, MerchantData::EAN]
            );
        }

        /**
         * Clear merchant's products that are in product index table and are not in the specific feed
         */
        $deleteWhere = [
            MerchantData::SELLER_ID . ' = ?' => $this->seller->getEntityId()
        ];

        if (!empty($productIds)) {
            $deleteWhere[MerchantData::PRODUCT_ID . ' NOT IN (?)'] = $productIds;
        }

        $this->_connection->delete(MerchantData::TABLE, $deleteWhere);
    }

    /**
     * Update stock data
     * @return void
     */
    private function updateStock(): void
    {
        $sku = [];
        if (!empty($this->_stockData)) {
            $sku = array_keys($this->_stockData);
            $this->_stockManagement->saveItems($this->_stockData);
        }

        $sourceCode = $this->seller->getSourceCode();

        if (!$sourceCode || 'default' === $sourceCode) {
            return;
        }

        $where = ['source_code = ?' => $sourceCode];

        if (!empty($sku)) {
            $where['sku NOT IN (?)'] = $sku;
        }

        $this->_connection->delete('inventory_source_item', $where);
    }

    /**
     * Get product sku/entity_id map
     * @param array $sku
     * @return array
     */
    private function _getProductMap(array $sku): array
    {
        $query = $this->_connection->select()
            ->from(
                ['e' => $this->_connection->getTableName('catalog_product_entity')],
                ['sku', 'entity_id']
            )
            ->where('e.sku IN (?)', $sku)
            ->where('e.type_id IN (?)', ['simple', 'virtual']);

        return $this->_connection->fetchPairs($query);
    }

    /**
     * @inheritDoc
     */
    public function processProduct(\Magento\Catalog\Model\Product $product): void
    {
        if (!$this->canProcessProduct($product)) {
            return;
        }

        $data = [
            MerchantData::PRODUCT_ID => $product->getId(),
            MerchantData::SELLER_ID => $product->getData(ItemInterface::DISTRIBUTOR_ID),
            MerchantData::PRICE => $product->getPrice(),
            MerchantData::SPECIAL_PRICE => $product->getSpecialPrice(),
            MerchantData::DELIVERY_ID => $product->getData(ItemInterface::ESTIMATED_DELIVERY),
            MerchantData::EAN => $product->getData(ItemInterface::EAN)
        ];

        $this->_connection->insertOnDuplicate(
            $this->_connection->getTableName(MerchantData::TABLE),
            $data,
            [MerchantData::PRICE, MerchantData::SPECIAL_PRICE, MerchantData::DELIVERY_ID, MerchantData::EAN]
        );
    }

    /**
     * Check if product can be processed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function canProcessProduct(\Magento\Catalog\Model\Product $product): bool
    {
        return $product->getId() &&
            in_array($product->getTypeId(), MerchantItemData::getAvailableProductTypesToIndex()) &&
            $product->hasData(ItemInterface::DISTRIBUTOR_ID);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processItem(ItemInterface $item, int $sellerId, int $productId): void
    {
        if (!empty($item->getVariations())){
            return;
        }

        $seller = $this->_sellerRepository->getById($sellerId);

        $priceInserts = [
            MerchantData::PRODUCT_ID => $productId,
            MerchantData::SELLER_ID => $sellerId,
            MerchantData::PRICE => $item->getPrice(),
            MerchantData::SPECIAL_PRICE => $item->getSpecialPrice(),
            MerchantData::DELIVERY_ID => $item->getEstimatedDelivery(),
            MerchantData::EAN => $item->getEan(),
        ];

        $sourceItem = $this->getStockItem($item, $seller->getSourceCode());

        $this->_connection->insertOnDuplicate(
            $this->_connection->getTableName(MerchantData::TABLE),
            $priceInserts,
            [MerchantData::PRICE, MerchantData::SPECIAL_PRICE, MerchantData::DELIVERY_ID, MerchantData::EAN]
        );

        $this->_stockManagement->saveItems([$sourceItem]);
    }
}
