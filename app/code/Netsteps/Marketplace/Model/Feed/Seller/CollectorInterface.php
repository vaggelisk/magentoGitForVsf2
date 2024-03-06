<?php
/**
 * CollectorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Seller;

/**
 * Interface CollectorInterface
 * @package Netsteps\Marketplace\Model\Feed\Seller
 */
interface CollectorInterface
{
    /**
     * Collect files from sellers
     * @return void
     */
    public function collect(): void;

    /**
     * @param int $sellerId
     * @return void
     */
    public function collectBySeller(int $sellerId): void;
}
