<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

interface SellerAdminInterface
{
    const ENTITY_ID = 'entity_id';
    const SELLER_ID = 'seller_id';
    const USER_ID = 'user_id';

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
    public function setSellerId(int $id): \Netsteps\Seller\Api\Data\SellerAdminInterface;

    /**
     * @return int
     */
    public function getAdminUserId(): int;

    /**
     * @param int $id
     * @return $this
     */
    public function setAdminUserId(int $id): \Netsteps\Seller\Api\Data\SellerAdminInterface;

}
