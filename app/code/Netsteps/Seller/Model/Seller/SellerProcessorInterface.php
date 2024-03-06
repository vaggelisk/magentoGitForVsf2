<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller;

use Netsteps\Seller\Api\Data\SellerInterface;

interface SellerProcessorInterface
{
    /**
     * @param SellerInterface $seller
     * @return void
     */
    public function execute(SellerInterface $seller):void;
}
