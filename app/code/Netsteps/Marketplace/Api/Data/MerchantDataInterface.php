<?php
/**
 * MerchantDataInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api\Data;

/**
 * Interface MerchantDataInterface
 * @package Netsteps\Marketplace\Api\Data
 */
interface MerchantDataInterface
{
    const TABLE = 'seller_product_index';
    const PRODUCT_ID = 'product_id';
    const SELLER_ID = 'seller_id';
    const EAN = 'ean';
    const PRICE = 'price';
    const SPECIAL_PRICE = 'special_price';
    const DELIVERY_ID = 'delivery_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const QUANTITY = 'quantity';
    const SOURCE_CODE = 'source_code';

    /**
     * Get product Id
     * @return int
     */
    public function getProductId(): int;

    /**
     * Get seller id
     * @return int
     */
    public function getSellerId(): int;

    /**
     * Get price
     * @return float
     */
    public function getPrice(): float;

    /**
     * Get special price (3rd price maybe is set during sales period)
     * @return float|null
     */
    public function getSpecialPrice(): ?float;

    /**
     * Get delivery id
     * @return int
     */
    public function getDeliveryId(): int;

    /**
     * Get ean number for product and merchant pair
     * @return string|null
     */
    public function getEan(): ?string;

    /**
     * Get creation date
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get modify date
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Get final price
     * @return float
     */
    public function getFinalPrice(): float;

    /**
     * Get source code
     * @return string|null
     */
    public function getSourceCode(): ?string;

    /**
     * Get available quantity
     * @return int|null
     */
    public function getQuantity(): ?int;

    /**
     * Get available product types (type_id array) that can be indexed
     * @return mixed
     */
    public static function getAvailableProductTypesToIndex(): array;
}
