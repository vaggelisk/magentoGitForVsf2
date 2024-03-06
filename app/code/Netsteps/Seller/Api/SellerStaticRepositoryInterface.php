<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api;

use Netsteps\Seller\Api\Data\SellerStaticInterface;

interface SellerStaticRepositoryInterface
{
    const TABLE_NAME = 'seller_entity_static';
    const REGISTRY_KEY = 'current_seller_static';

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerStaticInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id):\Netsteps\Seller\Api\Data\SellerStaticInterface;

    /**
     * @param int $id
     * @return SellerStaticInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySellerId(int $id):\Netsteps\Seller\Api\Data\SellerStaticInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerStaticInterface $seller
     * @return \Netsteps\Seller\Api\Data\SellerStaticInterface
     */
    public function save(\Netsteps\Seller\Api\Data\SellerStaticInterface $seller):\Netsteps\Seller\Api\Data\SellerStaticInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerStaticInterface $seller
     * @return bool
     * @throws \Exception
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerStaticInterface $seller):bool;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function deleteById(int $id):bool;

    /**
     * @return \Netsteps\Seller\Api\Data\SellerStaticInterface
     */
    public function getEmptySellerStaticModel(): \Netsteps\Seller\Api\Data\SellerStaticInterface;
}
