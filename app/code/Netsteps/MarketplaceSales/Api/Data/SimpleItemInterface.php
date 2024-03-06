<?php
/**
 * SimpleItemInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface SimpleItemInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface SimpleItemInterface
{
    const ITEM_ID = 'item_id';
    const QTY = 'qty';

    /**
     * Get order item id
     * @return int
     */
    public function getItemId(): int;

    /**
     * Get qty to refund
     * @return int
     */
    public function getQty(): int;

    /**
     * Set order item id
     * @param int $itemId
     * @return \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface
     */
    public function setItemId(int $itemId): \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface;

    /**
     * Set qty to refund
     * @param int $qty
     * @return \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface
     */
    public function setQty(int $qty): \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface;
}
