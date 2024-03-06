<?php
/**
 * OrderItemRegistryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api;

use Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;

/**
 * Interface OrderItemRegistryInterface
 * @package Netsteps\MarketplaceSales\Api
 */
interface OrderItemRegistryRepositoryInterface
{
    /**
     * Register a new relation between
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $cartItem
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return int|null
     */
    public function register(
        \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $cartItem,
        \Magento\Sales\Api\Data\OrderItemInterface $orderItem
    ): ?int;

    /**
     * Get registry by quote item id
     * @param int $itemId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface|
     */
    public function getByQuoteItemId(int $itemId): OrderItemRegistryInterface;

    /**
     * Get registry by order item id
     * @param int $itemId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function getByOrderItemId(int $itemId): OrderItemRegistryInterface;

    /**
     * Get an array of registered items based on order id
     * @param int $orderId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface[]
     */
    public function getRegistriesByOrderId(int $orderId): array;
}
