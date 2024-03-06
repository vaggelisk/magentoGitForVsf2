<?php
/**
 * ProductIndexRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api;

/**
 * Interface ProductIndexRepositoryInterface
 * @package Netsteps\Marketplace\Api
 */
interface ProductIndexRepositoryInterface
{
    /**
     * Get seller data best offer for a specific product.
     * IF $minQty is set then we will check to have a minimum quantity
     * @param int $productId
     * @param int|null $minQty
     * @return \Netsteps\Marketplace\Api\Data\MerchantDataInterface|null
     */
    public function getBestSellerDataByProductId(int $productId, ?int $minQty = null): ?\Netsteps\Marketplace\Api\Data\MerchantDataInterface;

    /**
     * Get all seller data for a product
     * @param int $productId
     * @return \Netsteps\Marketplace\Api\Data\MerchantDataInterface[]
     */
    public function getAllProductSellerData(int $productId): array;

    /**
     * Get seller best offer for product ids given.
     * If empty array passed then fetch for all products
     * @param int[] $productIds
     * @return \Traversable
     */
    public function getProductsBestOfferSeller(array $productIds = []): \Traversable;
}
