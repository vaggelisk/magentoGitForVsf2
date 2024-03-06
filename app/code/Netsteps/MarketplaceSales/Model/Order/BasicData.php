<?php
/**
 * BasicData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

use Magento\Framework\DataObject;
use Netsteps\MarketplaceSales\Api\Data\OrderBasicDataInterface;

/**
 * Class BasicData
 * @package Netsteps\MarketplaceSales\Model\Order
 */
class BasicData extends DataObject implements OrderBasicDataInterface
{

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->_getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getIncrementId(): string
    {
        return $this->_getData(self::INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getState(): string
    {
        return $this->_getData(self::STATE);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethod(): string
    {
        return $this->_getData(self::SHIPPING_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod(): string
    {
        return $this->_getData(self::PAYMENT_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function getItemCount(): int
    {
        return (int)$this->_getData(self::ITEM_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function getGrandTotal(): float
    {
        return (float)$this->_getData(self::GRAND_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function getCreationDate(): string
    {
        return $this->_getData(self::CREATION_DATE);
    }

    /**
     * Populate from given order
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return OrderBasicDataInterface
     */
    public function populateFromOrder(\Magento\Sales\Api\Data\OrderInterface $order): \Netsteps\MarketplaceSales\Api\Data\OrderBasicDataInterface
    {
        /** @var $order \Magento\Sales\Model\Order */
        return $this->setData(self::ORDER_ID, $order->getEntityId())
            ->setData(self::INCREMENT_ID, $order->getIncrementId())
            ->setData(self::STATE, $order->getState())
            ->setData(self::STATUS, $order->getStatus())
            ->setData(self::SHIPPING_METHOD, $order->getShippingMethod())
            ->setData(self::PAYMENT_METHOD, $order->getPayment()->getMethod())
            ->setData(self::GRAND_TOTAL, $order->getGrandTotal())
            ->setData(self::CREATION_DATE, $order->getCreatedAt());
    }
}
