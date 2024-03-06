<?php
/**
 * FeedRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api;

/**
 * Interface FeedRepositoryInterface
 * @package Netsteps\Marketplace\Api
 */
interface FeedRepositoryInterface
{
    /**
     * Save a feed object
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function save(\Netsteps\Marketplace\Api\Data\FeedInterface $feed): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Get a feed object by feed id
     * @param int $feedId
     * @param bool $force
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function get(int $feedId, bool $force = true): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Delete a feed by id
     * @param int $feedId
     * @return bool
     */
    public function deleteById(int $feedId): bool;

    /**
     * Get list of feeds
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null): \Magento\Framework\Api\SearchResultsInterface;

    /**
     * Get feed collection for given seller
     * @param int $sellerId
     * @return \Netsteps\Marketplace\Model\ResourceModel\Feed\Collection
     */
    public function getSellerFeedCollection(int $sellerId): \Netsteps\Marketplace\Model\ResourceModel\Feed\Collection;

    /**
     * Create an empty feed object
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function createEmptyFeed(): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Update feed status
     * @param Data\FeedInterface $feed
     * @param string $status
     * @return void
     */
    public function updateStatus(\Netsteps\Marketplace\Api\Data\FeedInterface $feed, string $status): void;
}


