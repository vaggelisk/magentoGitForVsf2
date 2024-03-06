<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

interface SellerStaticInterface
{

    const ENTITY_ID = 'entity_id';
    const SELLER_ID = 'seller_id';
    const IBAN = 'iban';
    const BENEFICIARY = 'beneficiary';
    const COMMISSION = 'commission';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getSelleryId():int;

    /**
     * @param int $id
     * @return $this
     */
    public function setSelleryId(int $id): \Netsteps\Seller\Api\Data\SellerStaticInterface;

    /**
     * @return string
     */
    public function getIban():string;

    /**
     * @param string $iban
     * @return $this
     */
    public function setIban(string $iban): \Netsteps\Seller\Api\Data\SellerStaticInterface;

    /**
     * @return string
     */
    public function getBeneficiary():string;

    /**
     * @param string $beneficiary
     * @return $this
     */
    public function setBeneficiary(string $beneficiary):\Netsteps\Seller\Api\Data\SellerStaticInterface;

    /**
     * @return float
     */
    public function getCommission():float;

    /**
     * @param int|float $value
     * @return $this
     */
    public function setCommission(int|float $value):\Netsteps\Seller\Api\Data\SellerStaticInterface;

}
