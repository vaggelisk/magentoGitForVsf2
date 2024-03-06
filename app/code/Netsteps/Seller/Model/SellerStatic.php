<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Model\AbstractModel;
use Netsteps\Seller\Api\Data\SellerStaticInterface;

class SellerStatic extends AbstractModel implements SellerStaticInterface
{
    protected $_eventPrefix = 'ns_seller_static';

    protected $_eventObject = 'seller_static';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\ResourceModel\SellerStatic::class);
    }

    /**
     * @inheritDoc
     */
    public function getSelleryId(): int
    {
        return (int)$this->getData(SellerStaticInterface::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSelleryId(int $id): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        $this->setData(SellerStaticInterface::SELLER_ID, $id);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIban(): string
    {
        return (string)$this->getData(SellerStaticInterface::IBAN);
    }

    /**
     * @inheritDoc
     */
    public function setIban(string $iban): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        $this->setData(SellerStaticInterface::IBAN, $iban);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBeneficiary(): string
    {
        return (string)$this->getData(SellerStaticInterface::BENEFICIARY);
    }

    /**
     * @inheritDoc
     */
    public function setBeneficiary(string $beneficiary): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        $this->setData(SellerStaticInterface::BENEFICIARY, $beneficiary);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCommission(): float
    {
        return (float)$this->getData(SellerStaticInterface::COMMISSION) ?? 0.0;
    }

    /**
     * @inheritDoc
     */
    public function setCommission(float|int $value): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        $this->setData(SellerStaticInterface::COMMISSION, $value);
        return $this;
    }
}
