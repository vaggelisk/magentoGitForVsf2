<?php
/**
 * ExpiredOrderDataInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Process;

/**
 * Interface ExpiredOrderDataInterface
 * @package Netsteps\MarketplaceSales\Model\Process
 */
interface ExpiredOrderDataInterface
{
    const SELLER_ID = 'seller_id';
    const ORDER_IDS = 'order_ids';

    /**
     * Get seller id
     * @return int
     */
    public function getSellerId(): int;

    /**
     * Get order ids
     * @return int[]
     */
    public function getOrderIds(): array;

    /**
     * Set seller id
     * @param int $sellerId
     * @return \Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface
     */
    public function setSellerId(int $sellerId): \Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface;

    /**
     * Set order ids
     * @param int[] $orderIds
     * @return \Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface
     */
    public function setOrderIds(array $orderIds): \Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface;
}
