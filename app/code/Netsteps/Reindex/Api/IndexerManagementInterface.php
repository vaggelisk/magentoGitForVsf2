<?php
/**
 * IndexerManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Reindex
 */

namespace Netsteps\Reindex\Api;

/**
 * Interface IndexerManagementInterface
 * @package Netsteps\Reindex\Api
 */
interface IndexerManagementInterface
{
    /**
     * Execute indexers
     * @param \Netsteps\Reindex\Api\Data\IndexerDataInterface[] $indexers
     * @return bool
     */
    public function execute(array $indexers): bool;
}
