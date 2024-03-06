<?php
/**
 * OrderProcessErrorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface OrderProcessErrorInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface OrderProcessErrorInterface
{
    const TABLE = 'marketplace_order_relation_error';

    const MAGENTO_ORDER_ID = 'magento_order_id';
    const SELLER_ID = 'seller_id';
    const ERROR_MESSAGE = 'message';
    const ERROR_TRACE = 'trace';

    /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * @return int
     */
    public function getSellerId(): int;

    /**
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * @return string|null
     */
    public function getErrorTrace(): ?string;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param int $orderId
     * @return  \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
     */
    public function setOrderId(int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface;

    /**
     * @param int $sellerId
     * @return  \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
     */
    public function setSellerId(int $sellerId): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface;

    /**
     * @param string $message
     * @return  \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
     */
    public function setErrorMessage(string $message): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface;

    /**
     * @param string|null $trace
     * @return  \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
     */
    public function setErrorTrace(?string $trace): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface;

    /**
     * @return array
     */
    public function getDataForInsertion(): array;
}
