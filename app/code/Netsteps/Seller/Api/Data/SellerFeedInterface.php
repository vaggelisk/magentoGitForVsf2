<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

interface SellerFeedInterface
{

    const ENTITY_ID = 'entity_id';
    const SELLER_ID = 'seller_id';
    const TYPE = 'type';
    const URL_PATH = 'url_path';
    const STORE_ID = 'store_id';

    /**
     * @return mixed
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getSellerId(): int;

    /**
     * @param int $id
     * @return $this
     */
    public function setSellerId(int $id): \Netsteps\Seller\Api\Data\SellerFeedInterface;

    /**
     * @return string
     */
    public function getType():string;

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): \Netsteps\Seller\Api\Data\SellerFeedInterface;

    /**
     * @return string
     */
    public function getUrlPath():string;

    /**
     * @param string $url
     * @return $this
     */
    public function setUrlPath(string $url): \Netsteps\Seller\Api\Data\SellerFeedInterface;

    /**
     * @return int
     */
    public function getStoreId():int;

    /**
     * @param int $id
     * @return $this
     */
    public function setStoreId(int $id): \Netsteps\Seller\Api\Data\SellerFeedInterface;
}
