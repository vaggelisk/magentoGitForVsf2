<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

interface SellerIntegrationInterface
{

    const ENTITY_ID = 'entity_id';
    const SELLER_ID = 'seller_id';
    const INTEGRATION_ID = 'integration_id';

    /**
     * @return mixed
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getSellerId():int;

    /**
     * @param int $id
     * @return $this
     */
    public function setSellerId(int $id):\Netsteps\Seller\Api\Data\SellerIntegrationInterface;

    /**
     * @return int
     */
    public function getIntegrationId():int;

    /**
     * @param int $id
     * @return $this
     */
    public function setIntegrationId(int $id):\Netsteps\Seller\Api\Data\SellerIntegrationInterface;

}
