<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Netsteps\Seller\Api\Data\SellerIntegrationInterface;

class SellerIntegration extends \Magento\Framework\Model\AbstractModel implements SellerIntegrationInterface
{


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\ResourceModel\SellerIntegration::class);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId(): int
    {
        return (int)$this->getData(SellerIntegrationInterface::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId(int $id): \Netsteps\Seller\Api\Data\SellerIntegrationInterface
    {
        $this->setData(SellerIntegrationInterface::SELLER_ID, $id);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIntegrationId(): int
    {
        return (int)$this->getData(SellerIntegrationInterface::INTEGRATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIntegrationId(int $id): \Netsteps\Seller\Api\Data\SellerIntegrationInterface
    {
        $this->setData(SellerIntegrationInterface::INTEGRATION_ID, $id);
        return $this;
    }
}
