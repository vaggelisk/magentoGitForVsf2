<?php
/**
 * FeedInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api\Data;

/**
 * Interface FeedInterface
 * @package Netsteps\Marketplace\Api\Data
 */
interface FeedInterface
{
    const TABLE = 'marketplace_feed';
    const EVENT_PREFIX = 'marketplace_feed';
    const EVENT_OBJECT = 'feed';
    const CACHE_TAG = 'm_feed';
    const ID = 'feed_id';

    const SELLER_ID = 'seller_id';
    const STATUS = 'status';
    const FEED_DATA = 'feed_data';
    const FILE_TYPE = 'file_type';
    const FEED_TYPE = 'feed_type';
    const ADDITIONAL_INFO = 'additional_info';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get feed id
     * @return int|null
     */
    public function getFeedId(): ?int;

    /**
     * Get related seller id
     * @return int
     */
    public function getSellerId(): int;

    /**
     * Get feed status
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get feed raw data as string
     * @return string
     */
    public function getFeedData(): string;

    /**
     * Get feed file type
     * @return string
     */
    public function getFileType(): string;

    /**
     * Get feed type
     * @return string
     */
    public function getFeedType(): string;

    /**
     * Get feed additional info
     * @return string|null
     */
    public function getAdditionalInfo(): ?string;

    /**
     * Get creation date
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get last modification date
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Set related seller id
     * @param int $sellerId
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function setSellerId(int $sellerId): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Set feed status
     * @param string $status
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function setStatus(string $status): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Set feed raw data as string representation
     * @param string $dataEncoded
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function setFeedData(string $dataEncoded): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Set feed file data type (file extension)
     * @param string $fileType
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function setFileType(string $fileType): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Set feed import type
     * @param string $type
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function setFeedType(string $type): \Netsteps\Marketplace\Api\Data\FeedInterface;

    /**
     * Set feed additional information
     * @param string|null $info
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface
     */
    public function setAdditionalInfo(?string $info): \Netsteps\Marketplace\Api\Data\FeedInterface;
}
