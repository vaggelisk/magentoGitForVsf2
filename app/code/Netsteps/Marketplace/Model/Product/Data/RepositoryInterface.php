<?php
/**
 * RepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Data;

/**
 * Interface RepositoryInterface
 * @package Netsteps\Marketplace\Model\Product\Data
 */
interface RepositoryInterface
{
    /**
     * Get retail prices of given product ids
     * @param int[] $productIds
     * @param int $storeId
     * @return float[]
     */
    public function getRetailPrices(array $productIds, int $storeId = 0): array;
}
