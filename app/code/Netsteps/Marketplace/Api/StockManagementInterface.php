<?php
/**
 * StockManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api;

/**
 * Interface StockManagementInterface
 * @package Netsteps\Marketplace\Api
 */
interface StockManagementInterface
{
    /**
     * Create source item
     * @param string $sku
     * @param int $qty
     * @param bool $isInStock
     * @param string|null $sourceCode
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface
     */
    public function createSourceItem(
        string $sku,
        int $qty,
        bool $isInStock,
        ?string $sourceCode = null
    ): \Magento\InventoryApi\Api\Data\SourceItemInterface;

    /**
     * Save source items massively
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterface[] $sourceItems
     * @return void
     */
    public function saveItems(array $sourceItems): void;

    /**
     * Get product stock qty
     * @param string $sku
     * @param string|null $sourceCode
     * @param bool $useMap
     * @return float|null
     */
    public function getProductStockQty(string $sku, ?string $sourceCode = null, bool $useMap = true): ?float;
}
