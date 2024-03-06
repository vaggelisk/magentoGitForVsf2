<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel\SellerIntegration;

use Netsteps\Seller\Api\SellerIntegrationRepositoryInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_mainTable = SellerIntegrationRepositoryInterface::TABLE_NAME;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\SellerIntegration::class, \Netsteps\Seller\Model\ResourceModel\SellerIntegration::class);
    }

}
