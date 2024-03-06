<?php
/**
 * OrderProcessErrorRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api;

/**
 * Interface OrderProcessErrorRepositoryInterface
 * @package Netsteps\MarketplaceSales\Api
 */
interface OrderProcessErrorRepositoryInterface
{
    /**
     * Save a single error
     * @param \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface $error
     * @return void
     */
    public function save(
        \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface $error
    ): void;

    /**
     * Save multiple errors
     * @param \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface[] $errors
     * @return void
     */
    public function saveMultiple(array $errors): void;

    /**
     * Get all errors for given order id
     * @param int $orderId
     * @param int|null $sellerId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface[]
     */
    public function getErrorsByOrderId(int $orderId, ?int $sellerId = null): array;
}
