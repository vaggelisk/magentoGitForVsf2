<?php
/**
 * OrderInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface OrderInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface OrderInterface
{
    const INCREMENT_ID = 'increment_id';
    const CREATED_AT = 'created_at';
    const EXPIRES_AT = 'expires_at';
    const PAYMENT_METHOD = 'payment_method';
    const SHIPPING_METHOD = 'shipping_method';
    const COMMENTS = 'comments';
    const GRAND_TOTAL = 'grand_total';
    const VAT_VALUE = 'vat_value';
    const COUPON_CODE = 'coupon_code';
    const IS_INVOICE = 'is_invoice';
    const STATUS = 'status';
    const PRODUCTS = 'products';
    const INVOICE_FIELDS = 'invoice_fields';
    const SHIPPING_ADDRESS = 'shipping_address';
    const BILLING_ADDRESS = 'billing_address';
    const TRACKS = 'tracks';


    /**
     * Get order's increment id
     * @return string
     */
    public function getIncrementId(): string;

    /**
     * Get order's created at
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get order's expires at
     * @return string
     */
    public function getExpiresAt(): string;

    /**
     * Get order's payment method
     * @return string
     */
    public function getPaymentMethod(): string;

    /**
     * Get order's shipping method
     * @return string
     */
    public function getShippingMethod(): string;

    /**
     * Get order's comments
     * @return string|null
     */
    public function getComments(): ?string;

    /**
     * Get order's grand total
     * @return float
     */
    public function getGrandTotal(): float;

    /**
     * Get order's vat value
     * @return float
     */
    public function getVatValue(): float;

    /**
     * Get order's coupon code
     * @return string|null
     */
    public function getCouponCode(): ?string;

    /**
     * Get order's is invoice
     * @return bool
     */
    public function getIsInvoice(): bool;

    /**
     * Get order's status
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get order's products
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface[]
     */
    public function getProducts(): array;

    /**
     * Get order's invoice fields
     * @return \Netsteps\MarketplaceSales\Api\Data\MetadataInterface[]
     */
    public function getInvoiceFields(): array;

    /**
     * Get order's shipping address
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface|null
     */
    public function getShippingAddress(): ?\Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Get order's billing address
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function getBillingAddress(): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Get tracks if order is shipped
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface[]
     */
    public function getTracks(): array;

    /**
     * Set order's increment id
     * @param string $incrementId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setIncrementId(string $incrementId): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's created at
     * @param string $createdAt
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setCreatedAt(string $createdAt): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's expires at
     * @param string $expiresAt
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setExpiresAt(string $expiresAt): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's payment method
     * @param string $paymentMethod
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setPaymentMethod(string $paymentMethod): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's shipping method
     * @param string $shippingMethod
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setShippingMethod(string $shippingMethod): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's comments
     * @param string|null $comments
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setComments(?string $comments): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's grand total
     * @param float $total
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setGrandTotal(float $total): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's vat value
     * @param float $vatValue
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setVatValue(float $vatValue): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's coupon code
     * @param string|null $coupon
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setCouponCode(?string $coupon): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's is invoice
     * @param bool $isInvoice
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setIsInvoice(bool $isInvoice): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's status
     * @param string $status
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setStatus(string $status): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's products
     * @param \Netsteps\MarketplaceSales\Api\Data\OrderProductInterface[] $products
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setProducts(array $products): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's invoice fields
     * @param \Netsteps\MarketplaceSales\Api\Data\MetadataInterface[] $fields
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setInvoiceFields(array $fields): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's shipping address
     * @param \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface $address
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setShippingAddress(\Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface $address): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's billing address
     * @param \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface $address
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setBillingAddress(\Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface $address): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

    /**
     * Set order's tracks if order is shipped
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterface[] $tracks
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderInterface
     */
    public function setTracks(array $tracks): \Netsteps\MarketplaceSales\Api\Data\OrderInterface;

}
