<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */
namespace Netsteps\Seller\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\Seller\Api\Data\SellerFeedInterface;
use Netsteps\Seller\Api\SellerFeedRepositoryInterface;

class SellerFeed extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SellerFeedRepositoryInterface::TABLE_NAME, SellerFeedInterface::ENTITY_ID);
    }
}
