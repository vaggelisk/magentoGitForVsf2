<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\Seller\Api\Data\SellerOptionInterface;
use Netsteps\Seller\Api\SellerOptionRepositoryInterface;

class SellerOption extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SellerOptionRepositoryInterface::TABLE_NAME, SellerOptionInterface::ENTITY_ID);
    }
}
