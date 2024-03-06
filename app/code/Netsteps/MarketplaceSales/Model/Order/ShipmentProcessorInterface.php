<?php
/**
 * ShipmentProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\MarketplaceSales\Model\Order;

/**
 * Interface ShipmentProcessorInterface
 * @package Netsteps\MarketplaceSales\Model\Order
 */
interface ShipmentProcessorInterface
{
    /**
     * Execute a full shipment to a given order
     * @param string $orderId
     * @param bool $notify
     * @param \Magento\Sales\Api\Data\ShipmentTrackCreationInterface[] $tracks
     * @return int
     */
    public function execute(string $orderId, bool $notify = false, array $tracks = []): int;
}
