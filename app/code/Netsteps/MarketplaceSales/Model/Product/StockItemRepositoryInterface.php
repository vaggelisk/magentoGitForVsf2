<?php
/**
 * StockItemRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Product;

use Magento\InventoryApi\Api\Data\SourceItemInterface;

/**
 * Interface StockItemRepositoryInterface
 * @package Netsteps\MarketplaceSales\Model\Product
 */
interface StockItemRepositoryInterface
{
    /**
     * Get stock source item by product entity id
     * Get source stock by item id
     * @param int $productId
     * @param string|null $sourceCode
     * @param float|null $qty
     * @param bool $raiseException
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface|null
     */
    public function getByProductId(int $productId, ?string $sourceCode = null, ?float $qty = null, bool $raiseException = false): ?\Magento\InventoryApi\Api\Data\SourceItemInterface;

    /**
     * Get stock source item by product sku
     * @param string $sku
     * @param string|null $sourceCode
     * @param float|null $qty
     * @param bool $raiseException
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface|null
     */
    public function get(string $sku, ?string $sourceCode = null, ?float $qty = null, bool $raiseException = false): ?\Magento\InventoryApi\Api\Data\SourceItemInterface;

    /**
     * Check if given sku has the necessary qty available
     * @param string $sku
     * @param float $qty
     * @param string|null $sourceCode
     * @return bool
     */
    public function isSaleable(string $sku, float $qty, ?string $sourceCode = null): bool;

    /**
     * Increase stock
     * @param int $sourceItemId
     * @param int $productId
     * @param string $sku
     * @param float $increment
     * @param bool $status
     * @return void
     */
    public function increaseStock(int $sourceItemId, int $productId, string $sku, float $increment, bool $status = true): void;

    /**
     * Revert items to previous condition
     * @param SourceItemInterface[] $items
     * @return void
     */
    public function revertItems(array $items): void;

    /**
     * Save a source item
     * @param SourceItemInterface $sourceItem
     * @return void
     */
    public function saveItem(SourceItemInterface $sourceItem): void;
}
