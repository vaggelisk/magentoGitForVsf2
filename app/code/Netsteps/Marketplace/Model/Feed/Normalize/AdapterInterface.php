<?php
/**
 * AdapterInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Normalize;

use Netsteps\Marketplace\Api\Data\FeedInterface;

/**
 * Interface AdapterInterface
 * @package Netsteps\Marketplace\Model\Feed\Normalize
 */
interface AdapterInterface
{
    /**
     * Execute an action upon a feed
     * @param FeedInterface $feed
     * @return void
     */
    public function execute(FeedInterface $feed): void;
}
