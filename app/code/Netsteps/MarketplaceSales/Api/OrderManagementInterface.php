<?php
/**
 * OrderManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api;

/**
 * Interface OrderManagementInterface
 * @package Netsteps\MarketplaceSales\Api
 */
interface OrderManagementInterface
{
    /**
     * Get order data based on magento's order auto increment id
     * @param int $id
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function getById(int $id): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Get order data based on magento's order's increment id
     * @param string $id
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function getByIncrementId(string $id): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Refund items in order by order increment id
     * @param string $orderId
     * @param \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface[] $items
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function refundById(string $orderId, array $items): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Refund items in order
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface[] $items
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function refund(\Magento\Sales\Api\Data\OrderInterface $order, array $items): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;
}
