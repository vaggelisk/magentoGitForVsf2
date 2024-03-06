<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Model\AbstractModel;
use Netsteps\Seller\Api\Data\SellerOptionInterface;

class SellerOption extends AbstractModel implements SellerOptionInterface
{
    protected $_eventPrefix = 'ns_seller_option';

    protected $_eventObject = 'seller_option';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\ResourceModel\SellerOption::class);
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return (int)$this->getData(SellerOptionInterface::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId(): int
    {
        return (int)$this->getData(SellerOptionInterface::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId(int $id): \Netsteps\Seller\Api\Data\SellerOptionInterface
    {
        $this->setData(SellerOptionInterface::SELLER_ID, $id);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptionName(): string
    {
        return (string)$this->getData(SellerOptionInterface::OPTION_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setOptionName(string $name): \Netsteps\Seller\Api\Data\SellerOptionInterface
    {
        $this->setData(SellerOptionInterface::OPTION_NAME, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptionValue()
    {
        return $this->getData(SellerOptionInterface::OPTION_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setOptionValue($value): \Netsteps\Seller\Api\Data\SellerOptionInterface
    {
        $this->setData(SellerOptionInterface::OPTION_VALUE, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStoreId(): int
    {
        return (int)$this->getData(SellerOptionInterface::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId(int $id): \Netsteps\Seller\Api\Data\SellerOptionInterface
    {
        $this->setData(SellerOptionInterface::STORE_ID, $id);
        return $this;
    }
}
