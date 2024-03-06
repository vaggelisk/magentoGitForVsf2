<?php
/**
 * Factory
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Reindex
 */

namespace Netsteps\Reindex\Model\Indexer;

use Netsteps\Reindex\Api\Data\IndexerDataInterfaceFactory as IndexerDataFactory;
use Netsteps\Reindex\Api\Data\IndexerDataInterface as IndexerData;

/**
 * Class Factory
 * @package Netsteps\Reindex\Model\Indexer
 */
class Factory
{
    /**
     * @var IndexerDataFactory
     */
    private IndexerDataFactory $_factory;

    /**
     * @param IndexerDataFactory $indexerDataFactory
     */
    public function __construct(IndexerDataFactory $indexerDataFactory) {
        $this->_factory = $indexerDataFactory;
    }

    /**
     * Create and indexer data object
     * @param string $indexerId
     * @param array $entityIds
     * @return IndexerData
     */
    public function create(string $indexerId, array $entityIds): IndexerData {
        /** @var  $indexerData IndexerData */
        $indexerData = $this->_factory->create();
        return $indexerData->setIndexerId($indexerId)->setEntityIds($entityIds);
    }
}
