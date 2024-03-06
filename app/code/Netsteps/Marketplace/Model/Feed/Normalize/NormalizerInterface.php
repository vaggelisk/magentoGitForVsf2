<?php
/**
 * NormalizerInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Normalize;

use Netsteps\Marketplace\Api\Data\FeedInterface;

/**
 * Interface NormalizerInterface
 * @package Netsteps\Marketplace\Model\Feed\Normalize
 */
interface NormalizerInterface
{
    /**
     * Normalize a feed
     * @param FeedInterface $feed
     * @param int|null $storeId
     * @return void
     */
    public function normalize(FeedInterface $feed, ?int $storeId = null): void;
}
