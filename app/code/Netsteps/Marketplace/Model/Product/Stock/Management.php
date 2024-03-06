<?php
/**
 * Management
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Stock;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Netsteps\Marketplace\Api\StockManagementInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface as SourceItem;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory as SourceItemFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface as SourceItemSaveHandler;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface as DefaultSourceProvider;
use Magento\Framework\App\ResourceConnection as ResourceConnection;

/**
 * Class Management
 * @package Netsteps\Marketplace\Model\Product\Stock
 */
class Management implements StockManagementInterface
{
    /**
     * @var array
     */
    private array $_sourceMap = [];

    /**
     * @var SourceItemFactory
     */
    private SourceItemFactory $_sourceItemFactory;

    /**
     * @var SourceItemSaveHandler
     */
    private SourceItemSaveHandler $_sourceItemSaveHandler;

    /**
     * @var DefaultSourceProvider
     */
    private DefaultSourceProvider $_defaultSourceProvider;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @param SourceItemFactory $sourceItemFactory
     * @param SourceItemSaveHandler $saveHandler
     * @param DefaultSourceProvider $defaultSourceProvider
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        SourceItemFactory     $sourceItemFactory,
        SourceItemSaveHandler $saveHandler,
        DefaultSourceProvider $defaultSourceProvider,
        ResourceConnection    $resourceConnection
    )
    {
        $this->_sourceItemFactory = $sourceItemFactory;
        $this->_sourceItemSaveHandler = $saveHandler;
        $this->_defaultSourceProvider = $defaultSourceProvider;
        $this->_connection = $resourceConnection->getConnection();
    }

    /**
     * @inheritDoc
     */
    public function createSourceItem(string $sku, int $qty, bool $isInStock, ?string $sourceCode = null): \Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        if (is_null($sourceCode) || trim($sourceCode) === '') {
            $sourceCode = $this->_defaultSourceProvider->getCode();
        }

        if ($qty < 0) {
            $qty = 0;
            $isInStock = false;
        }

        $status = $isInStock ? SourceItem::STATUS_IN_STOCK : SourceItem::STATUS_OUT_OF_STOCK;

        /** @var  $sourceItem SourceItem */
        $sourceItem = $this->_sourceItemFactory->create();
        $sourceItem->setSku($sku);
        $sourceItem->setQuantity($qty);
        $sourceItem->setSourceCode($sourceCode);
        $sourceItem->setStatus($status);

        return $sourceItem;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function saveItems(array $sourceItems): void
    {
        $this->_sourceItemSaveHandler->execute($sourceItems);
    }

    /**
     * @inheritDoc
     */
    public function getProductStockQty(string $sku, ?string $sourceCode = null, bool $useMap = true): ?float
    {
        if (!$sourceCode) {
            $sourceCode = $this->_defaultSourceProvider->getCode();
        }

        if ($useMap) {
            $this->initSourceMap($sourceCode);
        }

        $value = $this->fetchFromCache($sku, $sourceCode);

        if (!is_null($value)) {
            return $value;
        }

        $select = $this->_initSelect()
            ->where('main_table.sku = ?', $sku)
            ->where('main_table.source_code = ?', $sourceCode);

        $quantity = $this->_connection->fetchOne($select);

        if ($quantity !== false) {
            $this->_sourceMap[$sourceCode][$sku] = (float)$quantity;
            return $this->_sourceMap[$sourceCode][$sku];
        }

        return null;
    }

    /**
     * Fetch from cache
     * @param string $sku
     * @param string $sourceCode
     * @return float|null
     */
    protected function fetchFromCache(string $sku, string $sourceCode): ?float
    {
        return $this->_sourceMap[$sourceCode][$sku] ?? null;
    }

    /**
     * Initialize source map
     * @param string $sourceCode
     * @return void
     */
    private function initSourceMap(string $sourceCode): void
    {
        if (array_key_exists($sourceCode, $this->_sourceMap)) {
            return;
        }

        $select = $this->_initSelect()
            ->where('main_table.source_code = ?', $sourceCode)
            ->columns(['quantity', 'sku']);

        $stockData = [];

        foreach ($this->_connection->fetchAssoc($select) as $stockItemData) {
            $stockData[$stockItemData['sku']] = (float)$stockItemData['quantity'];
        }

        $this->_sourceMap[$sourceCode] = $stockData;
    }

    /**
     * Initialize select for source item
     * @return \Magento\Framework\DB\Select
     */
    private function _initSelect(): \Magento\Framework\DB\Select
    {
        return $this->_connection->select()
            ->from(
                ['main_table' => $this->_connection->getTableName('inventory_source_item')],
                ['quantity']
            );
    }
}
