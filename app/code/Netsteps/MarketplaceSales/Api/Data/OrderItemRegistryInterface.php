<?php
/**
 * OrderItemRegistryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface OrderItemRegistryInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface OrderItemRegistryInterface
{
    const TABLE = 'order_item_seller';
    const EVENT_PREFIX = 'order_item_seller_registry';
    const EVENT_OBJECT = 'registry';
    const CACHE_TAG = 'mp_ois';

    const ID = 'registry_id';
    const QUOTE_ITEM_ID = 'quote_item_id';
    const ORDER_ITEM_ID = 'order_item_id';
    const PARENT_ORDER_ID = 'parent_order_id';
    const ITEM_SELLER_ID = 'item_seller_id';
    const ESTIMATED_DELIVERY_ID = 'estimated_delivery_id';
    const SELLER_PRICE = 'seller_price';
    const SELLER_SPECIAL_PRICE = 'seller_special_price';

    /**
     * Get registry id
     * @return int|null
     */
    public function getRegistryId(): ?int;

    /**
     * Get quote item id
     * @return int
     */
    public function getQuoteItemId(): int;

    /**
     * Get order item id
     * @return int
     */
    public function getOrderItemId(): int;

    /**
     * Get order id
     * @return int
     */
    public function getOrderId(): int;

    /**
     * Get seller's id
     * @return int
     */
    public function getItemSellerId(): int;

    /**
     * Get seller's estimated delivery id
     * @return int
     */
    public function getEstimatedDeliveryId(): int;

    /**
     * Get seller's price
     * @return float
     */
    public function getSellerPrice(): float;

    /**
     * Get seller's special price
     * @return float|null
     */
    public function getSellerSpecialPrice(): ?float;

    /**
     * Set quote item id
     * @param int $itemId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function setQuoteItemId(int $itemId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;

    /**
     * Set order item id
     * @param int $itemId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function setOrderItemId(int $itemId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;

    /**
     * Set order id
     * @param int $orderId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function setOrderId(int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;

    /**
     *  Set seller id
     * @param int $sellerId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function setItemSellerId(int $sellerId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;

    /**
     * Set seller estimated delivery id
     * @param int $deliveryId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function setEstimatedDeliveryId(int $deliveryId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;

    /**
     * Set seller base price
     * @param float $price
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function setSellerPrice(float $price): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;

    /**
     * Set seller special price
     * @param float|null $price
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
     */
    public function setSellerSpecialPrice(?float $price): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;
}
