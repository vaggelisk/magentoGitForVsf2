<?php
/**
 * StockItemRepository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Product;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Phrase;
use Magento\Inventory\Model\ResourceModel\SourceItem as SourceItemResource;
use Magento\InventoryApi\Api\Data\SourceItemInterface as SourceItem;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory as SourceItemFactory;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface as SourceItemDefaultProvider;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite as WebsiteStockIdResolver;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Exception\OutOfStockException;
use Psr\Log\LoggerInterface;

/**
 * Class StockItemRepository
 * @package Netsteps\MarketplaceSales\Model\Product
 */
class StockItemRepository implements StockItemRepositoryInterface
{
    private string $errorMessage = 'Product with %1 has not the requested quantity available.';

    /**
     * @var SourceItemDefaultProvider
     */
    private SourceItemDefaultProvider $_sourceItemDefaultProvider;

    /**
     * @var SourceItemFactory
     */
    private SourceItemFactory $_sourceItemFactory;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @var SourceItemResource
     */
    private SourceItemResource $_sourceItemResource;

    /**
     * @var WebsiteStockIdResolver
     */
    private WebsiteStockIdResolver $_websiteStockIdResolver;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param SourceItemDefaultProvider $defaultSourceProvider
     * @param SourceItemFactory $sourceItemInterfaceFactory
     * @param WebsiteStockIdResolver $websiteStockIdResolver
     * @param SourceItemResource $sourceItemResource
     * @param ResourceConnection $resourceConnection
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        SourceItemDefaultProvider $defaultSourceProvider,
        SourceItemFactory         $sourceItemInterfaceFactory,
        WebsiteStockIdResolver    $websiteStockIdResolver,
        SourceItemResource        $sourceItemResource,
        ResourceConnection        $resourceConnection,
        LoggerPool                $loggerPool
    )
    {
        $this->_sourceItemDefaultProvider = $defaultSourceProvider;
        $this->_sourceItemFactory = $sourceItemInterfaceFactory;
        $this->_websiteStockIdResolver = $websiteStockIdResolver;
        $this->_sourceItemResource = $sourceItemResource;
        $this->_connection = $resourceConnection->getConnection();
        $this->_logger = $loggerPool->getLogger('debug');
    }

    /**
     * @inheritDoc
     * @throws OutOfStockException
     */
    public function getByProductId(
        int     $productId,
        ?string $sourceCode = null,
        ?float  $qty = null,
        bool    $raiseException = false
    ): ?\Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        $this->parseSourceCode($sourceCode);
        $select = $this->getDefaultSelect($sourceCode);
        $select->where('e.entity_id = ?', $productId);
        $exceptionMessage = __($this->errorMessage, "id: {$productId}");
        return $this->getStockItemFromSelect($select, $raiseException, $exceptionMessage);
    }

    /**
     * @inheritDoc
     * @throws OutOfStockException
     */
    public function get(
        string  $sku,
        ?string $sourceCode = null,
        ?float  $qty = null,
        bool    $raiseException = false
    ): ?\Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        $this->parseSourceCode($sourceCode);
        $select = $this->getDefaultSelect($sourceCode);
        $select->where('e.sku = ?', $sku);
        $this->addQtyCheckToSelect($select, $qty);
        $exceptionMessage = __($this->errorMessage, "sku: {$sku}");
        return $this->getStockItemFromSelect($select, $raiseException, $exceptionMessage);
    }

    /**
     * @inheritDoc
     */
    public function isSaleable(string $sku, float $qty, ?string $sourceCode = null): bool
    {
        $sourceItem = $this->get($sku, $sourceCode);
        return !is_null($sourceItem) && $sourceItem->getQuantity() > 0 && $sourceItem->getStatus() === 1;
    }

    /**
     * Get default select for stock item retrieve process
     * @param string $sourceCode
     * @return \Magento\Framework\Db\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDefaultSelect(string $sourceCode): \Magento\Framework\Db\Select
    {
        $entityJoinField = SourceItem::SKU;
        $fieldSourceCode = SourceItem::SOURCE_CODE;

        return $this->_connection->select()
            ->from(
                ['e' => $this->_connection->getTableName('catalog_product_entity')],
                []
            )
            ->join(
                ['si' => $this->_sourceItemResource->getMainTable()],
                "e.sku = si.{$entityJoinField} AND si.{$fieldSourceCode} = '{$sourceCode}'"
            );
    }

    /**
     * Parse source code
     * @param string|null $sourceCode
     * @return void
     */
    private function parseSourceCode(?string &$sourceCode = null): void
    {
        if (is_null($sourceCode)) {
            $sourceCode = $this->_sourceItemDefaultProvider->getCode();
        }
    }

    /**
     * Get stock item result from select
     * @param \Magento\Framework\DB\Select $select
     * @param bool $raiseException
     * @param Phrase|null
     * @return SourceItem|null
     * @throws OutOfStockException
     */
    private function getStockItemFromSelect(
        \Magento\Framework\DB\Select $select,
        bool                         $raiseException = false,
        ?Phrase                      $errorMessage = null
    ): ?\Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        $data = $this->_connection->fetchRow($select);

        if (!is_array($data) || empty($data)) {
            if ($raiseException) {
                $errorMessage = $errorMessage ?? __('Requested quantity is not available');
                throw new OutOfStockException($errorMessage);
            }
            return null;
        }
        return $this->_sourceItemFactory->create(['data' => $data]);
    }

    /**
     * Add qty check to select
     * @param \Magento\Framework\DB\Select $select
     * @param float|null $qty
     * @return void
     */
    private function addQtyCheckToSelect(\Magento\Framework\DB\Select $select, ?float $qty): void
    {
        if (!is_null($qty)) {
            $qtyField = SourceItem::QUANTITY;
            $select->where("si.{$qtyField} >= ?", $qty);
        }
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function increaseStock(int $sourceItemId, int $productId, string $sku, float $increment, bool $status = true): void
    {
        if ($increment < 0.01) {
            return;
        }

        $stockId = $this->_websiteStockIdResolver->execute();

        $binds = [
            SourceItem::STATUS => (int)$status,
            SourceItem::QUANTITY => new \Zend_Db_Expr(SourceItem::QUANTITY . " + {$increment}")
        ];
        $where = ['source_item_id = ?' => $sourceItemId];

        $this->_connection->update($this->_sourceItemResource->getMainTable(), $binds, $where);
        if ($stockId === 1) {
            $this->modifyCatalogInventoryStockStatusTable($productId, $increment);
        } else {
            $this->modifyInventoryStockStatusTable($sku, $stockId, $increment);
        }
    }

    /**
     * Modify catalog inventory stock status table to trigger update on view table
     * @param int $productId
     * @param float $amount
     * @param bool $isDecrease
     * @return void
     */
    private function modifyCatalogInventoryStockStatusTable(
        int   $productId,
        float $amount,
        bool  $isDecrease = false
    ): void
    {
        $table = $this->_connection->getTableName('cataloginventory_stock_status');
        $operator = $isDecrease ? '-' : '+';

        $binds = [
            'qty' => new \Zend_Db_Expr("qty {$operator} {$amount}"),
            'stock_status' => new \Zend_Db_Expr("IF (qty {$operator} {$amount} > 0, 1, 0)")
        ];

        $where = [
            'website_id = ?' => 0,
            'stock_id = ?' => 1,
            'product_id = ?' => $productId
        ];

        $this->_connection->update($table, $binds, $where);
    }

    /**
     * Modify inventory stock status table
     * @param string $sku
     * @param int $stockId
     * @param float $amount
     * @param bool $isDecrease
     * @return void
     */
    private function modifyInventoryStockStatusTable(
        string $sku,
        int    $stockId,
        float  $amount,
        bool   $isDecrease = false
    ): void
    {
        if ($stockId < 2) {
            return;
        }

        $table = $this->_connection->getTableName("inventory_stock_{$stockId}");
        $operator = $isDecrease ? '-' : '+';

        $binds = [
            'quantity' => new \Zend_Db_Expr("quantity {$operator} {$amount}"),
            'is_salable' => new \Zend_Db_Expr("IF (quantity {$operator} {$amount} > 0, 1, 0)")
        ];

        $where = ['sku = ?' => $sku];

        $this->_connection->update($table, $binds, $where);
    }


    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveItem(SourceItem $sourceItem): void
    {
        $this->_sourceItemResource->save($sourceItem);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function revertItems(array $items): void
    {
        $stockId = $this->_websiteStockIdResolver->execute();

        foreach ($items as $stockItem) {
            $stockItem->setHasDataChanges(true);
            $this->_sourceItemResource->save($stockItem);

            $amountModified = $stockItem->getAmountModified();

            if (!$amountModified || $amountModified <= 0) {
                continue;
            }

            if ($stockId === 1) {
                $productId = $stockItem->getModifiedProductId();
                if (!$productId){
                    continue;
                }
                $this->modifyCatalogInventoryStockStatusTable($productId, $amountModified, true);
            } else {
                $this->modifyInventoryStockStatusTable($stockItem->getSku(), $stockId, $amountModified, true);
            }
        }
    }
}
