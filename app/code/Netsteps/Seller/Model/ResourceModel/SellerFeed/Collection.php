<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel\SellerFeed;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\SellerFeed::class, \Netsteps\Seller\Model\ResourceModel\SellerFeed::class);
    }
}
