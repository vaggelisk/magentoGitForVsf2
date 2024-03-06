<?php
/**
 * OrderRelationInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface OrderRelationInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface OrderRelationInterface
{
    const TABLE = 'marketplace_order_relation';
    const CACHE_TAG = 'mp_order';
    const EVENT_PREFIX = 'order_relation';
    const EVENT_OBJECT = 'relation';

    const ID  = 'relation_id';
    const MAGENTO_ORDER_ID = 'magento_order_id';
    const IS_MAIN_ORDER = 'is_main_order';
    const IS_PROCESSED = 'is_processed';
    const PARENT_ORDER_ID = 'parent_order_id';
    const SELLER_ID = 'seller_id';
    const NUM_OF_TRIES = 'num_of_tries';
    const PROCESSED_SELLER_IDS = 'processed_seller_ids';
    const RELATION_CREATED_AT = 'relation_created_at';
    const RELATION_UPDATED_AT = 'relation_updated_at';


    /**
     * Get order relation id
     * @return int|null
     */
    public function getRelationId(): ?int;

    /**
     * Get magento order id
     * @return int
     */
    public function getMagentoOrderId(): int;

    /**
     * Get relation if it is a main order
     * @return bool
     */
    public function getIsMainOrder(): bool;

    /**
     * Get relation if it is processed flag
     * @return bool
     */
    public function getIsProcessed(): bool;

    /**
     * Get relation parent order id
     * @return int|null
     */
    public function getParentOrderId(): ?int;

    /**
     * Get related seller id (only for children)
     * @return int|null
     */
    public function getSellerId(): ?int;

    /**
     * Get the number of tries that this order tried to be processed
     * @return int
     */
    public function getNumberOfTries(): int;

    /**
     * Get processed seller ids
     * @return int[]
     */
    public function getProcessedSellerIds(): array;

        /**
     * Get relation creation date
     * @return string
     */
    public function getRelationCreatedAt(): string;

    /**
     * Get relation modified date
     * @return string
     */
    public function getRelationUpdatedAt(): string;

    /**
     * Set magento order id
     * @param int $orderId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
     */
    public function setMagentoOrderId(int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

    /**
     * Set is main order
     * @param bool $isMain
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
     */
    public function setIsMainOrder(bool $isMain): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

    /**
     * Set is processed
     * @param bool $isProcessed
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
     */
    public function setIsProcessed(bool $isProcessed): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

    /**
     * Set parent magento order id
     * @param int|null $orderId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
     */
    public function setParentOrderId(?int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

    /**
     * Set order seller id
     * @param int|null $sellerId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
     */
    public function setSellerId(?int $sellerId): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

    /**
     * Set the number of tries that this order tried to be processed
     * @param int $numOfTries
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
     */
    public function setNumberOfTries(int $numOfTries): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

    /**
     * Set processed seller ids
     * @param array $sellerIds
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
     */
    public function setProcessedSellerIds(array $sellerIds = []): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;

    /**
     * Increase num of tries
     * @return OrderRelationInterface
     */
    public function increaseTries(): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;
}
