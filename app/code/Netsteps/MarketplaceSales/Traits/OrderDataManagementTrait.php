<?php
/**
 * OrderDataManagementTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Traits;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Netsteps\MarketplaceSales\Api\Data\OrderBasicDataInterface;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;
use Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface;
use Netsteps\MarketplaceSales\Model\Order\Status\Management;

/**
 * Trait OrderDataManagementTrait
 * @package Netsteps\MarketplaceSales\Traits
 */
trait OrderDataManagementTrait
{
    /**
     * Get order relation from order object
     * @param OrderInterface $order
     * @return OrderRelationInterface|null
     */
    protected function getOrderRelation(OrderInterface $order): ?OrderRelationInterface
    {
        return $order->getExtensionAttributes()->getMarketplaceRelation();
    }

    /**
     * Get child orders data
     * @param OrderInterface $order
     * @return OrderBasicDataInterface[]
     */
    protected function getChildOrdersData(OrderInterface $order): array {
        return $order->getExtensionAttributes()->getChildOrders() ?? [];
    }

    /**
     * Get order seller items
     * @param OrderInterface $order
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface[]
     */
    protected function getOrderSellerItems(OrderInterface $order): array
    {
        return $order->getExtensionAttributes()->getSellerItems() ?? [];
    }

    /**
     * Check if order can be approved
     * @param OrderInterface $order
     * @return bool
     */
    protected function canApprove(OrderInterface $order): bool {
        return $order->getStatus() === OrderStatusManagementInterface::STATUS_PENDING_APPROVAL;
    }

    /**
     * Check if order can be declined
     * @param OrderInterface $order
     * @return bool
     */
    protected function canDecline(OrderInterface $order): bool {
        return $order->getStatus() === OrderStatusManagementInterface::STATUS_PENDING_APPROVAL;
    }

    /**
     * Get order's item grouped by seller id
     * @param OrderInterface $order
     * @param array $ignoredSellerIds
     * @return array
     */
    protected function getOrderItemsGroupedBySeller(OrderInterface $order, array $ignoredSellerIds = []): array
    {
        $grouped = [];
        $orderItems = $order->getItemsCollection();

        foreach ($this->getOrderSellerItems($order) as $sellerItem) {
            if (
                !$sellerItem->getItemSellerId() ||
                !$sellerItem->getOrderItemId() ||
                in_array($sellerItem->getItemSellerId(), $ignoredSellerIds)) {
                continue;
            }

            /** @var  $orderItem \Magento\Sales\Model\Order\Item */
            $orderItem = $orderItems->getItemById($sellerItem->getOrderItemId());
            $grouped[$sellerItem->getItemSellerId()][] = $orderItem->getParentItem() ?? $orderItem;
        }

        return $grouped;
    }

    /**
     * Check if order can be shipped
     * @param OrderInterface $order
     * @return bool
     */
    protected function canShipOrInvoice(OrderInterface $order): bool
    {
        if ($order->getData('marketplace_ignore_check')){
            return true;
        }

        $relation = $this->getOrderRelation($order);

        if (!$relation) {
            return false;
        }

        if ($relation->getIsMainOrder() && $relation->getIsProcessed()) {
            $children = $this->getChildOrdersData($order);
            $childrenCount = count($children);
            $canShip = $childrenCount && $childrenCount === count($relation->getProcessedSellerIds());

            foreach ($children as $child) {
                $canShip &= in_array(
                    $child->getStatus(),
                    Management::getChildrenApprovedStatuses()
                );
            }

            return $canShip;
        }

        if (
            !$relation->getIsMainOrder() &&
            $order->getStatus() !== OrderStatusManagementInterface::STATUS_PENDING_APPROVAL
        ) {
            return true;
        }

        return false;
    }
}
