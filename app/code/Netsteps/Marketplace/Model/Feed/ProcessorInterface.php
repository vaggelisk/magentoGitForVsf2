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
interface ProcessorInterface
{
    /**
     * Process all pending xml
     * @return void
     */
    public function processFull(): void;

    /**
     * Process a list of feed ids
     * @param int[] $ids
     * @return void
     */
    public function processList(array $ids): void;

    /**
     * Process single feed
     * @param int $id
     * @return void
     */
    public function processOne(int $id): void;
}
