<?php
/**
 * OrderItemDataManagementTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Traits;

use Netsteps\MarketplaceSales\Model\Product\SellerManagementInterface;
use Netsteps\Marketplace\Model\Product\Attribute\Source\EstimatedDelivery;

/**
 * Trait OrderItemDataManagementTrait
 * @package Netsteps\MarketplaceSales\Traits
 */
trait OrderItemDataManagementTrait
{
    /**
     * Export product seller data
     * @param \Magento\Sales\Model\Order\Item $item
     * @return array
     */
    protected function exportSellerInfoData(\Magento\Sales\Model\Order\Item $item): array
    {
        $sellerInfoData = [];

        $order = $item->getOrder();

        $normalizedItem = $this->getItemThatHasSellerData($item, $order);

        $infoBuyRequest = $this->getItemInfoBuyRequest($normalizedItem);

        if (!$infoBuyRequest) {
            return [];
        }

        if ($sellerInfo = @$infoBuyRequest['seller_info']) {
            $sellerInfoData = [
                'seller_info' => $sellerInfo,
                'seller_source' => @$sellerInfo['source_code'],
                'seller_id' => @$sellerInfo['seller_id']
            ];
        }

        return $sellerInfoData;
    }

    /**
     * Get item info buy request
     * @param \Magento\Sales\Model\Order\Item $item
     * @return array
     */
    protected function getItemInfoBuyRequest(\Magento\Sales\Model\Order\Item $item): array {
        return $item->getProductOptionByCode('info_buyRequest') ?? [];
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order\Item
     */
    protected function getItemThatHasSellerData(\Magento\Sales\Model\Order\Item $item, \Magento\Sales\Model\Order $order): \Magento\Sales\Model\Order\Item
    {
        if (!$item->getParentItemId()) {
            $itemCollection = $order->getItemsCollection();
            $item = $itemCollection->getItemByColumnValue('parent_item_id', $item->getItemId()) ?? $item;
        }
        return $item;
    }

    /**
     * Get item discount amount
     * @param \Magento\Sales\Model\Order\Item $item
     * @return float
     */
    protected function getItemDiscountAmount(\Magento\Sales\Model\Order\Item $item): float {
        return (float)(
            $this->getItemInfoBuyRequest($item)[SellerManagementInterface::PARENT_ITEM_DISCOUNT_AMOUNT] ??
            $item->getDiscountAmount()
        );
    }

    /**
     * Get item discount percent
     * @param \Magento\Sales\Model\Order\Item $item
     * @return float
     */
    protected function getItemDiscountPercent(\Magento\Sales\Model\Order\Item $item): float {
        return (float)(
            $this->getItemInfoBuyRequest($item)[SellerManagementInterface::PARENT_ITEM_DISCOUNT_PERCENT] ??
            $item->getDiscountPercent()
        );
    }

    /**
     * Get item original price amount
     * @param \Magento\Sales\Model\Order\Item $item
     * @return float
     */
    protected function getItemOriginalPrice(\Magento\Sales\Model\Order\Item $item): float {
        return (float)(
            $this->getItemInfoBuyRequest($item)[SellerManagementInterface::PARENT_ITEM_ORIGINAL_PRICE] ??
            $item->getPriceInclTax()
        );
    }

    /**
     * Get item original EAN
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string|null
     */
    protected function getItemOriginalEan(\Magento\Sales\Model\Order\Item $item): ?string {
        return $this->getItemInfoBuyRequest($item)[SellerManagementInterface::PARENT_ITEM_EAN] ?? null;
    }

    /**
     * Get item EAN
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string|null
     */
    protected function getItemEan(\Magento\Sales\Model\Order\Item $item): ?string {
        $sellerInfo = $this->exportSellerInfoData($item);

        if (!$sellerInfo) {
            return null;
        }

        return $sellerInfo['seller_info']['ean'] ?? ($sellerInfo['ean'] ?? null);
    }

    /**
     * Get number of deliveries depends on unique different seller ids
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    protected function getNumberOfDeliveries(\Magento\Sales\Api\Data\OrderInterface $order): int {
        $sellers = [];

        foreach ($order->getItems() as $item) {
            $sellerInfo = $this->exportSellerInfoData($item);
            $sellerId = $sellerInfo['seller_id'] ?? false;

            if (is_numeric($sellerId)) {
                $sellers[] = $sellerId;
            }
        }

        return count(array_unique($sellers));
    }

    /**
     * Get delivery data
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    protected function getDeliveryData(\Magento\Sales\Api\Data\OrderInterface $order): array {
        $deliveryIds = [];

        /** @var  $item \Magento\Sales\Model\Order\Item */
        foreach ($order->getItems() as $item) {
            $sellerInfo = $this->exportSellerInfoData($item);
            $deliveryId = $sellerInfo['seller_info']['delivery_id'] ?? null;

            if ($deliveryId){
                $deliveryIds[] = (int)$deliveryId;
            }
        }

        $deliveryId = empty($deliveryIds) ? EstimatedDelivery::DELIVERY_AVAILABLE : max(array_unique($deliveryIds));
        $days = EstimatedDelivery::DAY_MAP[$deliveryId] ?? '1-3';

        return [
            'label' => __('Delivery in %1 days', $days),
            'days' => $days
        ];
    }
}
