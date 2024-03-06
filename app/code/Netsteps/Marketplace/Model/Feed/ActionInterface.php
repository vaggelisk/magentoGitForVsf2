<?php
/**
 * ActionInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

/**
 * Interface ActionInterface
 * @package Netsteps\Marketplace\Model\Feed
 */
interface ActionInterface
{
    /**
     * Process feed action
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
     * @return void
     */
    public function execute(
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
    ): void;

    /**
     * Validate feed for structure and data
     * @param string $sellerGroup
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
     * @return array
     */
    public function validate(
        string $sellerGroup,
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
    ): array;
}
