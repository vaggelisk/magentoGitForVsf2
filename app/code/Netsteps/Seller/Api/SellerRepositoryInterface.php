<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */
namespace Netsteps\Seller\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface SellerRepositoryInterface
{
    const TABLE_NAME = 'seller_entity';

    const REGISTRY_KEY = 'current_seller';

    /**
     * @param int $id
     * @param bool $fullData
     * @return \Netsteps\Seller\Api\Data\SellerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id, bool $fullData = false):\Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Netsteps\Seller\Api\Data\SellerSearchResultsInterface
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null): \Netsteps\Seller\Api\Data\SellerSearchResultsInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerInterface $seller
     * @return \Netsteps\Seller\Api\Data\SellerInterface
     */
    public function save(\Netsteps\Seller\Api\Data\SellerInterface $seller):\Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerInterface $seller
     * @return bool
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerInterface $seller):bool;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $id):bool;

    /**
     * @return \Netsteps\Seller\Api\Data\SellerInterface
     */
    public function getEmptySellerModel(): \Netsteps\Seller\Api\Data\SellerInterface;

}
