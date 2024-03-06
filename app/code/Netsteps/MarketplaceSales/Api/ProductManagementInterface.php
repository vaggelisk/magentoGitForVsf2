<?php
/**
 * ProductManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api;

/**
 * Interface ProductManagementInterface
 * @package Netsteps\MarketplaceSales\Api
 */
interface ProductManagementInterface
{
    const LOWEST_SELLER_ID = 'lowest_seller_id';
    const LOWEST_SELLER_DATA = 'lowest_seller_data';
    const SELLER_DISCOUNT = 'seller_discount';
    const IS_VISIBLE_IN_FRONT = 'is_visible_in_front';

    /**
     * Get lowest seller id
     * @param int $productId
     * @param bool $force
     * @return int|null
     */
    public function getLowestSellerId(int $productId, bool $force = false): ?int;

    /**
     * Get lowest seller data
     * @param int $productId
     * @param bool $force
     * @return array
     */
    public function getLowestSellerData(int $productId, bool $force = false): array;

    /**
     * Update product with seller's lowest data
     * @param int $productId
     * @return void
     */
    public function updateSellerProductData(int $productId): void;

    /**
     * Update product seller data for all products given
     * If no product ids given then update data for the whole catalog.
     *
     * @param int[] $productIds
     * @return void
     */
    public function updateProductsData(array $productIds = []): void;
}
