<?php
/**
 * IndexerDataInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Reindex
 */

namespace Netsteps\Reindex\Api\Data;

/**
 * Interface IndexerDataInterface
 * @package Netsteps\Reindex\Api\Data
 */
interface IndexerDataInterface
{
    const INDEXER_ID = 'indexer_id';
    const ENTITY_IDS = 'entity_ids';

    /**
     * Get indexer id to execute
     * @return string
     */
    public function getIndexerId(): string;

    /**
     * Get entity ids to execute indexer for
     * @return int[]
     */
    public function getEntityIds(): array;

    /**
     * Set indexer id to execute
     * @param string $indexerId
     * @return \Netsteps\Reindex\Api\Data\IndexerDataInterface
     */
    public function setIndexerId(string $indexerId): \Netsteps\Reindex\Api\Data\IndexerDataInterface;

    /**
     * Set entity ids to that execute indexer for
     * @param int[] $entityIds
     * @return \Netsteps\Reindex\Api\Data\IndexerDataInterface
     */
    public function setEntityIds(array $entityIds): \Netsteps\Reindex\Api\Data\IndexerDataInterface;
}
