<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerFeedInterface;

interface SellerFeedRepositoryInterface
{
    const TABLE_NAME = 'seller_feeds';

    /**
     * @param int $id
     * @return \Netsteps\Seller\Api\Data\SellerFeedInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): \Netsteps\Seller\Api\Data\SellerFeedInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Netsteps\Seller\Api\Data\SellerFeedSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria): \Netsteps\Seller\Api\Data\SellerFeedSearchResultsInterface;

    /**
     * @param int $sellerId
     * @return \Netsteps\Seller\Api\Data\SellerFeedInterface[]
     */
    public function getBySellerId(int $sellerId): array;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerFeedInterface  $seller
     * @return \Netsteps\Seller\Api\Data\SellerFeedInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function save(\Netsteps\Seller\Api\Data\SellerFeedInterface $seller):\Netsteps\Seller\Api\Data\SellerFeedInterface;

    /**
     * @param \Netsteps\Seller\Api\Data\SellerFeedInterface $seller
     * @return bool
     * @throws \Exception
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerFeedInterface $seller):bool;

}
