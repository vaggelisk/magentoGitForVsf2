<?php
/**
 * SplitOrderInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

/**
 * Interface SplitOrderInterface
 * @package Netsteps\MarketplaceSales\Model\Order
 */
interface SplitOrderManagementInterface
{
    /**
     * Split all orders that are not already processed
     * @return array
     */
    public function splitFull(): array;

    /**
     * Split single order
     * @param int $orderId
     * @return array
     */
    public function splitSingle(int $orderId): array;
}
