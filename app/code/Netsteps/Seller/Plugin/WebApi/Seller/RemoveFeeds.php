<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Plugin\WebApi\Seller;

class RemoveFeeds
{

    /**
     * @param \Netsteps\Seller\Api\Data\SellerInterface $subject
     * @param array $result
     * @return array
     */
    public function afterGetFeeds(\Netsteps\Seller\Api\Data\SellerInterface $subject, $result):array
    {
        return [];
    }

}
