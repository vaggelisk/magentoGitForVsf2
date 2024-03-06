<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api;

interface ProductDistributorResolverInterface
{
    /**
     * @param string $sku
     * @return \Netsteps\Seller\Api\Data\SellerInterface
     */
    public function getBySku(string $sku):?\Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerInterface
     */
    public function getByProductId(int $id):?\Netsteps\Seller\Api\Data\SellerInterface;
}
