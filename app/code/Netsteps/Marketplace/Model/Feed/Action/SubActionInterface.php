<?php
/**
 * SubActionInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action;

use Netsteps\Seller\Api\Data\SellerInterface;

/**
 * Interface SubActionInterface
 * @package Netsteps\Marketplace\Model\Feed\Action
 */
interface SubActionInterface
{
    /**
     * Process data for seller
     * @param array $data
     * @param SellerInterface $seller
     * @return void
     */
    public function process(array $data, SellerInterface $seller): void;
}
