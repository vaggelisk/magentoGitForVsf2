<?php
/**
 * OrderDataRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Process\Resource;

/**
 * Interface OrderDataRepositoryInterface
 * @package Netsteps\MarketplaceSales\Model\Process\Resource
 */
interface OrderDataRepositoryInterface
{
    /**
     * Get expired pending approval order by seller
     * @param array $incrementIds
     * @return \Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface[]
     */
    public function getExpiredPendingApproval(array $incrementIds = []): array;
}
