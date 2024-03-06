<?php
/**
 * FeedConfigurationInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\System\Config;

/**
 * Interface FeedConfigurationInterface
 * @package Netsteps\Marketplace\Model\System\Config
 */
interface FeedConfigurationInterface
{
    const FIELD_SELLER_NORMALIZED = 'seller_normalized';

    /**
     * Get an array of seller ids that are selected to normalize their data
     * @param int|null $storeId
     * @return int[]
     */
    public function getSellersForFeedNormalization(?int $storeId = null): array;
}
