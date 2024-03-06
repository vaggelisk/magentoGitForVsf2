<?php
/**
 * ModifierInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Model\Indexer\Data;

/**
 * Interface ModifierInterface
 * @package Netsteps\MarketplaceVSFbridge\Model\Indexer\Data
 */
interface ModifierInterface
{
    /**
     * Modify data
     * @param array $data
     * @return void
     */
    public function modify(array &$data): void;
}
