<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Model\AbstractModel;
use Netsteps\Seller\Api\Data\SellerAdminInterface;

class SellerAdmin extends AbstractModel implements SellerAdminInterface
{
    protected $_eventPrefix = 'ns_seller_admin';

    protected $_eventObject = 'seller_admin';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\ResourceModel\SellerAdmin::class);
    }

    /**
     * @return int
     */
    public function getSellerId(): int
    {
        return (int)$this->getData(SellerAdminInterface::SELLER_ID);
    }

    /**
     * @param int $id
     * @return SellerAdminInterface
     */
    public function setSellerId(int $id): \Netsteps\Seller\Api\Data\SellerAdminInterface
    {
        $this->setData(SellerAdminInterface::SELLER_ID, $id);
        return $this;
    }

    /**
     * @return int
     */
    public function getAdminUserId(): int
    {
        return (int)$this->getData(SellerAdminInterface::USER_ID);
    }

    /**
     * @param int $id
     * @return SellerAdminInterface
     */
    public function setAdminUserId(int $id): \Netsteps\Seller\Api\Data\SellerAdminInterface
    {
        $this->setData(SellerAdminInterface::USER_ID, $id);
        return $this;
    }
}
