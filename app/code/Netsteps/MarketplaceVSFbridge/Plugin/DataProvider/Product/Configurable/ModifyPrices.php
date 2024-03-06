<?php
/**
 * ModifyPrices
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Plugin\DataProvider\Product\Configurable;

/**
 * Class ModifyPrices
 * @package Netsteps\MarketplaceVSFbridge\Plugin\DataProvider\Product\Configurable
 */
class ModifyPrices
{
    /**
     * After execute preparation for configurable children data modify the prices of parent product
     * based on children lowest_seller_data.
     *
     * @param \Divante\VsbridgeIndexerCatalog\Model\Indexer\DataProvider\Product\Configurable\PrepareConfigurableProduct $configurableProduct
     * @param array $result
     * @return array
     */
    public function afterExecute(
        \Divante\VsbridgeIndexerCatalog\Model\Indexer\DataProvider\Product\Configurable\PrepareConfigurableProduct $configurableProduct,
        array $result
    ): array {
        $this->modifyPrices($result);
        return $result;
    }

    /**
     * Modify prices to index
     * @param array $productData
     * @return void
     */
    private function modifyPrices(array &$productData): void {
        $minPrice = null;
        $maxPrice = null;

        $children = $productData['configurable_children'] ?? [];

        if (empty($children)){
            return;
        }

        foreach ($children as &$child) {
            if (empty($child['lowest_seller_data'])){
                continue;
            }

            $sellerData = $child['lowest_seller_data'];
            $child['regular_price'] = (float)$sellerData['price'];
            $child['price'] = (float)$sellerData['price'];
            $child['final_price'] = (float)$sellerData['final_price'];

            $minPrice = !$minPrice ? $sellerData['final_price'] : min($minPrice, $sellerData['final_price']);
            $maxPrice = !$maxPrice ? $sellerData['final_price'] : max($maxPrice, $sellerData['final_price']);
        }

        $productData['configurable_children'] = $children;
        $productData['min_price'] = $minPrice ?? $productData['final_price'];
        $productData['max_price'] = $maxPrice ?? $productData['regular_price'];
    }
}
