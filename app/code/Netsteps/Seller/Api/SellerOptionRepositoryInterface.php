<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api;

interface SellerOptionRepositoryInterface
{
    const TABLE_NAME = 'seller_entity_options';

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerOptionInterface|null
     */
    public function getById(int $id):?\Netsteps\Seller\Api\Data\SellerOptionInterface;

    /**
     * @param int $sellerId
     * @param int|null $storeId
     * @return \Netsteps\Seller\Api\Data\SellerOptionInterface[]
     */
    public function getBySellerId(int $sellerId, ?int $storeId = null): array;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerOptionInterface $option
     * @return \Netsteps\Seller\Api\Data\SellerOptionInterface
     */
    public function save(\Netsteps\Seller\Api\Data\SellerOptionInterface $option):\Netsteps\Seller\Api\Data\SellerOptionInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerOptionInterface $option
     * @return bool
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerOptionInterface $option): bool;

    /**
     * @return \Netsteps\Seller\Api\Data\SellerOptionInterface
     */
    public function getEmptyOptionModel(): \Netsteps\Seller\Api\Data\SellerOptionInterface;
}
