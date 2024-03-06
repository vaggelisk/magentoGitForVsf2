<?php
/**
 * ProductHistoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api\Data;

/**
 * Interface ProductHistoryInterface
 * @package Netsteps\Marketplace\Api\Data
 */
interface ProductHistoryInterface
{
    const TABLE = 'marketplace_product_index';
    const EVENT_PREFIX = 'marketplace_product_history';
    const EVENT_OBJECT = 'history_item';
    const CACHE_TAG = 'mhi';

    const ID = 'product_sku';
    const VERSION_CODE = 'version_code';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get product sku
     * @return string
     */
    public function getProductSku(): string;

    /**
     * Get product version
     * @return string
     */
    public function getVersionCode(): string;

    /**
     * Get creation date
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get modification date
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Set product sku
     * @param string $sku
     * @return ProductHistoryInterface
     */
    public function setProductSku(string $sku): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface;

    /**
     * Set product version code
     * @param string $versionCode
     * @return ProductHistoryInterface
     */
    public function setVersionCode(string $versionCode): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface;
}
