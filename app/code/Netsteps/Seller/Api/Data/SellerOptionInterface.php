<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

interface SellerOptionInterface
{
    const ENTITY_ID = 'entity_id';
    const SELLER_ID = 'seller_id';
    const OPTION_NAME = 'option_name';
    const OPTION_VALUE = 'option_value';
    const STORE_ID = 'store_id';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getSellerId(): int;

    /**
     * @param int $id
     * @return $this
     */
    public function setSellerId(int $id): \Netsteps\Seller\Api\Data\SellerOptionInterface;

    /**
     * @return string
     */
    public function getOptionName():string;

    /**
     * @param string $name
     * @return $this
     */
    public function setOptionName(string $name):\Netsteps\Seller\Api\Data\SellerOptionInterface;

    /**
     * @return mixed
     */
    public function getOptionValue();

    /**
     * @param mixed $value
     * @return $this
     */
    public function setOptionValue($value):\Netsteps\Seller\Api\Data\SellerOptionInterface;

    /**
     * @return int
     */
    public function getStoreId():int;

    /**
     * @param int $id
     * @return $this
     */
    public function setStoreId(int $id):\Netsteps\Seller\Api\Data\SellerOptionInterface;

}
