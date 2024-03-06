<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface SellerAdminRepositoryInterface
{

    const TABLE_NAME = 'seller_entity_admin_user';

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerAdminInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id);

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerAdminInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySellerId(int $id):\Netsteps\Seller\Api\Data\SellerAdminInterface;

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerAdminInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByUserId(int $id):\Netsteps\Seller\Api\Data\SellerAdminInterface;

    /**
     * @param int $id
     * @param bool $fullData
     * @return \Netsteps\Seller\Api\Data\SellerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerByUserId(int $id, bool $fullData = false): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerAdminInterface $data
     * @return \Netsteps\Seller\Api\Data\SellerAdminInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(\Netsteps\Seller\Api\Data\SellerAdminInterface $data): \Netsteps\Seller\Api\Data\SellerAdminInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerAdminInterface $data
     * @return bool
     * @throws \Exception
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerAdminInterface $data): bool;

}
