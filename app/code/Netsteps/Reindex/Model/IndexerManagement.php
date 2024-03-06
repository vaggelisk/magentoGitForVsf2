<?php
/**
 * IndexerManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Reindex
 */

namespace Netsteps\Reindex\Model;

use Netsteps\Reindex\Api\IndexerManagementInterface;

use Magento\Indexer\Model\IndexerFactory as IndexerFactory;
use Magento\Indexer\Model\Indexer as Indexer;
use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;
use Magento\Indexer\Model\Indexer\Collection as IndexerCollection;

/**
 * Class IndexerManagement
 * @package Netsteps\Reindex\Model
 */
class IndexerManagement implements IndexerManagementInterface
{
    /**
     * @var IndexerFactory
     */
    private IndexerFactory $_indexerFactory;

    /**
     * @var IndexerCollectionFactory
     */
    private IndexerCollectionFactory $_indexerCollectionFactory;

    /**
     * @var IndexerCollection|null
     */
    private ?IndexerCollection $_collection = null;

    /**
     * @param IndexerFactory $indexerFactory
     * @param IndexerCollectionFactory $indexerCollectionFactory
     */
    public function __construct(
        IndexerFactory           $indexerFactory,
        IndexerCollectionFactory $indexerCollectionFactory
    )
    {
        $this->_indexerFactory = $indexerFactory;
        $this->_indexerCollectionFactory = $indexerCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $indexers): bool
    {
        foreach ($indexers as $indexer) {
            $entityIds = $indexer->getEntityIds();

            if (empty($entityIds)) {
                continue;
            }

            $indexerModel = $this->getIndexerModel($indexer->getIndexerId());

            if (!$indexerModel) {
                continue;
            }

            $indexerModel->reindexList($entityIds);
        }

        return true;
    }

    /**
     * Get indexer model
     * @param string $indexerId
     * @return Indexer|null
     */
    private function getIndexerModel(string $indexerId): ?Indexer
    {
        $indexerIds = $this->_getIndexerCollection()->getAllIds();

        if (!in_array($indexerId, $indexerIds)){
            return null;
        }

        /** @var  $indexerModel Indexer */
        $indexerModel = $this->_indexerFactory->create();
        $indexerModel->load($indexerId);
        return $indexerModel;
    }

    /**
     * @return IndexerCollection
     */
    private function _getIndexerCollection(): IndexerCollection
    {
        if (!$this->_collection) {
            $this->_collection = $this->_indexerCollectionFactory->create();
        }
        return $this->_collection;
    }
}
