<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\Seller\Api\Data\SellerStaticInterface;
use Netsteps\Seller\Api\SellerStaticRepositoryInterface;

class SellerStatic extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SellerStaticRepositoryInterface::TABLE_NAME, SellerStaticInterface::ENTITY_ID);
    }
}
