<?php
/**
 * SellerManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Product;

/**
 * Interface SellerManagementInterface
 * @package Netsteps\MarketplaceSales\Model\Product
 */
interface SellerManagementInterface
{
    const INFO_BUY_REQUEST_SELLER_ID = 'seller_id';
    const IGNORE_STOCK_CHECK = 'seller_ignore_stock_check';
    const PARENT_ITEM_ID = 'split_parent_item_id';
    const PARENT_ITEM_SOURCE_CODE = 'split_parent_item_source_code';
    const PARENT_ITEM_EAN = 'split_parent_item_ean';
    const PARENT_ITEM_DISCOUNT_PERCENT = 'split_parent_item_discount_percent';
    const PARENT_ITEM_DISCOUNT_AMOUNT = 'split_parent_item_discount_amount';
    const PARENT_ITEM_ORIGINAL_PRICE = 'split_parent_item_original_price';

    /**
     * Initialize product with seller data
     * @param \Magento\Catalog\Model\Product $product
     * @param int $sellerId
     * @param \Magento\Framework\DataObject $request
     * @return \Netsteps\Marketplace\Api\Data\MerchantDataInterface|null
     */
    public function initProductForSeller(
        \Magento\Catalog\Model\Product $product,
        int $sellerId,
        \Magento\Framework\DataObject $request
    ): ?\Netsteps\Marketplace\Api\Data\MerchantDataInterface;

    /**
     * Check if is valid the available qty for this seller
     * @param \Magento\Catalog\Model\Product $product
     * @param int $sellerId
     * @param float $qty
     * @return bool
     */
    public function isValid(\Magento\Catalog\Model\Product $product, int $sellerId, float $qty): bool;
}
