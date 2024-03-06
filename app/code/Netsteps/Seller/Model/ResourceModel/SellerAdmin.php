<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\Seller\Api\Data\SellerAdminInterface;
use Netsteps\Seller\Api\SellerAdminRepositoryInterface;

class SellerAdmin extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SellerAdminRepositoryInterface::TABLE_NAME, SellerAdminInterface::ENTITY_ID);
    }

}
