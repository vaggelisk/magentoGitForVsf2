<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel\Seller;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netsteps\Seller\Api\SellerRepositoryInterface;

class Collection extends AbstractCollection
{
    protected $_mainTable = SellerRepositoryInterface::TABLE_NAME;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\Seller::class, \Netsteps\Seller\Model\ResourceModel\Seller::class);
    }
}
