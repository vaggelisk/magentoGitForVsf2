<?php
/**
 * OrderProductInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface OrderProductInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface OrderProductInterface
{
    const ITEM_ID = 'item_id';
    const NAME = 'name';
    const SKU = 'sku';
    const QTY = 'qty';
    const QTY_REFUNDED = 'qty_refunded';
    const QTY_SHIPPED = 'qty_shipped';
    const EAN = 'ean';
    const PRICE = 'price';
    const PRICE_AFTER_DISCOUNT = 'price_after_discount';
    const ROW_TOTAL = 'row_total';
    const VAT_VALUE = 'vat_value';
    const VAT_PERCENT = 'vat_percent';
    const OPTIONS = 'options';

    /**
     * Get order item's id
     * @return int
     */
    public function getItemId(): int;

    /**
     * Get product's name
     * @return string
     */
    public function getName(): string;

    /**
     * Get product's sku
     * @return string
     */
    public function getSku(): string;

    /**
     * Get product's qty
     * @return float
     */
    public function getQty(): float;

    /**
     * Get product's qty refunded
     * @return float
     */
    public function getQtyRefunded(): float;

    /**
     * Get product's qty shipped
     * @return float
     */
    public function getQtyShipped(): float;

    /**
     * Get product's ean
     * @return string|null
     */
    public function getEan(): ?string;

    /**
     * Get product's price
     * @return float
     */
    public function getPrice(): float;

    /**
     * Get product's price_after_discount
     * @return float
     */
    public function getPriceAfterDiscount(): float;

    /**
     * Get product's row_total
     * @return float
     */
    public function getRowTotal(): float;

    /**
     * Get product's vat_value
     * @return float
     */
    public function getVatValue(): float;

    /**
     * Get product's vat_percent
     * @return float
     */
    public function getVatPercent(): float;

    /**
     * Get product's options
     * @return \Netsteps\MarketplaceSales\Api\Data\MetadataInterface[]
     */
    public function getOptions(): array;

    /**
     * Set magento's order item id
     * @param int $id
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setItemId(int $id): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's name
     * @param string $name
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setName(string $name): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's sku
     * @param string $sku
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setSku(string $sku): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's qty
     * @param float $qty
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setQty(float $qty): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's qty refunded
     * @param float $qty
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setQtyRefunded(float $qty): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's qty shipped
     * @param float $qty
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setQtyShipped(float $qty): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's ean
     * @param string|null $ean
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setEan(?string $ean): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's price
     * @param float $price
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setPrice(float $price): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's price_after_discount
     * @param float $price
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setPriceAfterDiscount(float $price): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's row_total
     * @param float $total
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setRowTotal(float $total): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's vat_value
     * @param float $vatValue
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setVatValue(float $vatValue): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's vat_percent
     * @param float $percent
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setVatPercent(float $percent): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;

    /**
     * Set product's options
     * @param $options \Netsteps\MarketplaceSales\Api\Data\MetadataInterface[]
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface
     */
    public function setOptions(array $options): \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface;
}
