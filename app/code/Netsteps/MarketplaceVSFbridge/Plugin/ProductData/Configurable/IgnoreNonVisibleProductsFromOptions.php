<?php
/**
 * IgnoreNonVisibleProductsFromOptions
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Plugin\ProductData\Configurable;

use Netsteps\MarketplaceSales\Api\ProductManagementInterface;

/**
 * Class IgnoreNonVisibleProductsFromOptions
 * @package Netsteps\MarketplaceVSFbridge\Plugin\ProductData\Configurable
 */
class IgnoreNonVisibleProductsFromOptions
{
    public function beforeExecute(
        \Divante\VsbridgeIndexerCatalog\Model\Indexer\DataProvider\Product\Configurable\LoadConfigurableOptions $loadConfigurableOptions,
        string $attributeCode,
        int $storeId,
        array $configurableChildren
    ): array {
        $configurableChildren = array_filter($configurableChildren, [$this, 'filterOnlyVisibleProducts']);
        return [$attributeCode, $storeId, $configurableChildren];
    }

    /**
     * Check only is visible in front children products
     * @param array $data
     * @return bool
     */
    protected function filterOnlyVisibleProducts(array $data): bool {
        $lowestSellerData = $data[ProductManagementInterface::LOWEST_SELLER_DATA] ?? [];
        return !empty($lowestSellerData);
    }
}
