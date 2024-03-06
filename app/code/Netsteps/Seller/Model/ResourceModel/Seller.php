<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;

class Seller extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SellerRepositoryInterface::TABLE_NAME, SellerInterface::ENTITY_ID);
    }
}
