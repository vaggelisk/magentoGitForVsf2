<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller;

use Netsteps\Seller\Api\Data\SellerInterface;

interface SellerValidatorInterface
{
    /**
     * @param SellerInterface $seller
     * @return void
     */
    public function validate(SellerInterface $seller):void;
}
