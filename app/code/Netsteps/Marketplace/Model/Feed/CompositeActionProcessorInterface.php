<?php
/**
 * ProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

/**
 * Interface ProcessorInterface
 * @package Netsteps\Marketplace\Model\Feed
 */
interface CompositeActionProcessorInterface
{
    /**
     * Process given feed
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @return void
     */
    public function process(\Netsteps\Marketplace\Api\Data\FeedInterface $feed): void;
}
