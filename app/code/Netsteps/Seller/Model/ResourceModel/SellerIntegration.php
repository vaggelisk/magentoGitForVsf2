<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel;

use Netsteps\Seller\Api\Data\SellerIntegrationInterface;
use Netsteps\Seller\Api\SellerIntegrationRepositoryInterface;

class SellerIntegration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SellerIntegrationRepositoryInterface::TABLE_NAME, SellerIntegrationInterface::ENTITY_ID);
    }

}
