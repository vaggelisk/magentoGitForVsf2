<?php
/**
 * OrderItemRegistry
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model;

use Magento\Framework\Model\AbstractModel;
use Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderItemRegistry as ResourceModel;

/**
 * Class OrderItemRegistry
 * @package Netsteps\MarketplaceSales\Model
 */
class OrderItemRegistry extends AbstractModel implements OrderItemRegistryInterface
{
    protected $_idFieldName = OrderItemRegistryInterface::ID;

    protected $_eventPrefix = OrderItemRegistryInterface::EVENT_PREFIX;

    protected $_eventObject = OrderItemRegistryInterface::EVENT_OBJECT;

    protected $_cacheTag = OrderItemRegistryInterface::CACHE_TAG;


    /**
     * Initialize resource
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getRegistryId(): ?int
    {
        return $this->getId() ? (int)$this->getId() : null;
    }

    /**
     * @inheritDoc
     */
    public function getQuoteItemId(): int
    {
        return (int)$this->_getData(self::QUOTE_ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function getOrderItemId(): int
    {
        return (int)$this->_getData(self::ORDER_ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function getItemSellerId(): int
    {
        return (int)$this->_getData(self::ITEM_SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getEstimatedDeliveryId(): int
    {
        return (int)$this->_getData(self::ESTIMATED_DELIVERY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getSellerPrice(): float
    {
        return (float)$this->_getData(self::SELLER_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getSellerSpecialPrice(): ?float
    {
        $price = $this->_getData(self::SELLER_SPECIAL_PRICE);
        return $price ? (float)$price : null;
    }

    /**
     * @inheritDoc
     */
    public function setQuoteItemId(int $itemId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
    {
        return $this->setData(self::QUOTE_ITEM_ID, $itemId);
    }

    /**
     * @inheritDoc
     */
    public function setOrderItemId(int $itemId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
    {
        return $this->setData(self::ORDER_ITEM_ID, $itemId);
    }

    /**
     * @inheritDoc
     */
    public function setItemSellerId(int $sellerId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
    {
        return $this->setData(self::ITEM_SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function setEstimatedDeliveryId(int $deliveryId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
    {
        return $this->setData(self::ESTIMATED_DELIVERY_ID, $deliveryId);
    }

    /**
     * @inheritDoc
     */
    public function setSellerPrice(float $price): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
    {
        if ($price <= 0){
            return $this;
        }
        return $this->setData(self::SELLER_PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function setSellerSpecialPrice(?float $price): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
    {
        if (!is_null($price) && $price <= 0){
            return $this;
        }
        return $this->setData(self::SELLER_SPECIAL_PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->_getData(self::PARENT_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId(int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface
    {
        return $this->setData(self::PARENT_ORDER_ID, $orderId);
    }
}
