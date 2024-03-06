<?php
/**
 * Product
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order;

use Magento\Framework\DataObject;
use Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

/**
 * Class Product
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order
 */
class Product extends DataObject implements OrderProductInterface
{
    /**
     * @inheritDoc
     */
    public function getItemId(): int
    {
        return $this->_getData(self::ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->_getData(self::NAME);

    }

    /**
     * @inheritDoc
     */
    public function getSku(): string
    {
        return $this->_getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function getQty(): float
    {
        return (float)$this->_getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function getQtyRefunded(): float
    {
        return $this->_getData(self::QTY_REFUNDED);
    }

    /**
     * @inheritDoc
     */
    public function getQtyShipped(): float
    {
        return $this->_getData(self::QTY_SHIPPED);
    }

    /**
     * @inheritDoc
     */
    public function getEan(): ?string
    {
        return $this->_getData(self::EAN);
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): float
    {
        return (float)$this->_getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getPriceAfterDiscount(): float
    {
        return (float)$this->_getData(self::PRICE_AFTER_DISCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getRowTotal(): float
    {
        return (float)$this->_getData(self::ROW_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function getVatValue(): float
    {
        return (float)$this->_getData(self::VAT_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function getVatPercent(): float
    {
        return (float)$this->_getData(self::VAT_PERCENT);
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->_getData(self::OPTIONS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setItemId(int $id): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::ITEM_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setSku(string $sku): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function setQty(float $qty): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function setQtyRefunded(float $qty): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::QTY_REFUNDED, $qty);
    }

    /**
     * @inheritDoc
     */
    public function setQtyShipped(float $qty): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::QTY_SHIPPED, $qty);
    }

    /**
     * @inheritDoc
     */
    public function setEan(?string $ean): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::EAN, $ean);
    }

    /**
     * @inheritDoc
     */
    public function setPrice(float $price): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function setPriceAfterDiscount(float $price): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::PRICE_AFTER_DISCOUNT, $price);
    }

    /**
     * @inheritDoc
     */
    public function setRowTotal(float $total): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::ROW_TOTAL, $total);
    }

    /**
     * @inheritDoc
     */
    public function setVatValue(float $vatValue): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::VAT_VALUE, $vatValue);
    }

    /**
     * @inheritDoc
     */
    public function setVatPercent(float $percent): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::VAT_PERCENT, $percent);
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
    {
        return $this->setData(self::OPTIONS, $options);
    }
}
