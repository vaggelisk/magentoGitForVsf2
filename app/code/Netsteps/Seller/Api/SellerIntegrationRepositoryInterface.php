<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api;

interface SellerIntegrationRepositoryInterface
{

    const TABLE_NAME = 'seller_entity_integration';

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerIntegrationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id):\Netsteps\Seller\Api\Data\SellerIntegrationInterface;

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerIntegrationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySellerId(int $id):\Netsteps\Seller\Api\Data\SellerIntegrationInterface;

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerIntegrationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByIntegrationId(int $id):\Netsteps\Seller\Api\Data\SellerIntegrationInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerIntegrationInterface $data
     * @return \Netsteps\Seller\Api\Data\SellerIntegrationInterface
     */
    public function save(\Netsteps\Seller\Api\Data\SellerIntegrationInterface $data):\Netsteps\Seller\Api\Data\SellerIntegrationInterface;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $id):bool;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Netsteps\Seller\Api\Data\SellerIntegrationSearchResultsInterface
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null): Data\SellerIntegrationSearchResultsInterface;

}
