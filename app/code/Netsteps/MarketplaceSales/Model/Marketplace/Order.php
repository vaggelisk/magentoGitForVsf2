<?php
/**
 * Order
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace;

use Magento\Framework\DataObject;
use Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;
use Netsteps\MarketplaceSales\Api\Data\OrderInterface;

/**
 * Class Order
 * @package Netsteps\MarketplaceSales\Model\Marketplace
 */
class Order extends DataObject implements OrderInterface
{

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
    public function getCreatedAt(): string
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getExpiresAt(): string
    {
        return $this->_getData(self::EXPIRES_AT);
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
    public function getShippingMethod(): string
    {
        return $this->_getData(self::SHIPPING_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function getComments(): ?string
    {
        return $this->_getData(self::COMMENTS);
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
    public function getVatValue(): float
    {
        return (float)$this->_getData(self::VAT_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function getCouponCode(): ?string
    {
        return $this->_getData(self::COUPON_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getIsInvoice(): bool
    {
        return (bool)(int)$this->_getData(self::IS_INVOICE);
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
    public function getProducts(): array
    {
        return $this->_getData(self::PRODUCTS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceFields(): array
    {
        return $this->_getData(self::INVOICE_FIELDS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getShippingAddress(): ?\Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->_getData(self::SHIPPING_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress(): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->_getData(self::BILLING_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setIncrementId(string $incrementId): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function setExpiresAt(string $expiresAt): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::EXPIRES_AT, $expiresAt);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethod(string $paymentMethod): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethod(string $shippingMethod): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * @inheritDoc
     */
    public function setComments(?string $comments): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::COMMENTS, $comments);
    }

    /**
     * @inheritDoc
     */
    public function setGrandTotal(float $total): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::GRAND_TOTAL, $total);
    }

    /**
     * @inheritDoc
     */
    public function setVatValue(float $vatValue): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::VAT_VALUE, $vatValue);
    }

    /**
     * @inheritDoc
     */
    public function setCouponCode(?string $coupon): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::COUPON_CODE, $coupon);
    }

    /**
     * @inheritDoc
     */
    public function setIsInvoice(bool $isInvoice): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::IS_INVOICE, $isInvoice);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function setProducts(array $products): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::PRODUCTS, $products);
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceFields(array $fields): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::INVOICE_FIELDS, $fields);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAddress(OrderAddressInterface $address): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::SHIPPING_ADDRESS, $address);
    }

    /**
     * @inheritDoc
     */
    public function setBillingAddress(OrderAddressInterface $address): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::BILLING_ADDRESS, $address);
    }

    /**
     * @inheritDoc
     */
    public function getTracks(): array
    {
        return $this->_getData(self::TRACKS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setTracks(array $tracks): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        return $this->setData(self::TRACKS, $tracks);
    }
}
