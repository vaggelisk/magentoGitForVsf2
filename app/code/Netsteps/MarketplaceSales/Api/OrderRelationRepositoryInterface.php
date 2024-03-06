<?php
/**
 * OrderRelationRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api;

use Magento\Sales\Api\Data\OrderInterface;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

/**
 * Interface OrderRelationRepositoryInterface
 * @package Netsteps\MarketplaceSales\Api
 */
interface OrderRelationRepositoryInterface
{
    /**
     * Save an order relation
     * @param OrderRelationInterface $orderRelation
     * @return OrderRelationInterface
     */
    public function save(OrderRelationInterface $orderRelation): OrderRelationInterface;

    /**
     * Get children orders of given order
     * @param OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getChildrenOrders(OrderInterface $order): array;

    /**
     * Get children orders of given order id
     * @param int $parentId
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getChildrenOrdersByParentId(int $parentId): array;

    /**
     * Link orders
     * @param OrderInterface $order
     * @param \Magento\Sales\Api\Data\OrderInterface[] $orders
     * @return int[]
     */
    public function linkOrders(OrderInterface $order, array $orders): array;

    /**
     * Register a new order to process
     * @param OrderInterface $order
     * @return int
     */
    public function registerOrder(OrderInterface $order): int;

    /**
     * Get parent order based on child order
     * @param OrderInterface $order
     * @param bool $useFallback
     * @return OrderInterface|null
     */
    public function getParentOrder(OrderInterface $order, bool $useFallback = false): ?OrderInterface;

    /**
     * Get parent order for given order id
     * @param int $orderId
     * @param bool $useFallback
     * @return OrderInterface|null
     */
    public function getParentOrderByOrderId(int $orderId, bool $useFallback = false): ?OrderInterface;

    /**
     * Get order relation record by order id
     * @param int $orderId
     * @return OrderRelationInterface|null
     */
    public function getRelationByOrderId(int $orderId): ?OrderRelationInterface;

    /**
     * Get order ids that are marked as main
     * If processedStatus is passed then return only order ids that are main and have
     * been processed if processedStatus = true else if processed status = false
     * then will return main order ids that are not processed yet
     * @param bool|null $processedStatus
     * @return OrderRelationInterface[]
     */
    public function getMainOrders(?bool $processedStatus = null): array;
}
