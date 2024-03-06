<?php
/**
 * ReindexItems
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Reindex
 */

namespace Netsteps\Reindex\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\Reindex\Api\Data\IndexerDataInterface;
use Psr\Log\LoggerInterface as Logger;
use Netsteps\Reindex\Api\IndexerManagementInterface as IndexerManagement;

/**
 * Class ReindexItems
 * @package Netsteps\MarketplaceVSFbridge\Observer
 */
class ReindexItems implements ObserverInterface
{
    /**
     * @var IndexerManagement
     */
    private IndexerManagement $_indexerManagement;

    /**
     * @var Logger
     */
    private Logger $_logger;

    /**
     * @param IndexerManagement $indexerManagement
     * @param Logger $logger
     */
    public function __construct(
        IndexerManagement $indexerManagement,
        Logger $logger
    )
    {
        $this->_indexerManagement = $indexerManagement;
        $this->_logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer): void
    {
        $indexers = $observer->getData('indexers') ?? [];

        if (!$indexers){
            return;
        }

        if (!is_array($indexers)) {
            $indexers = [$indexers];
        }

        /** @var  $filteredIndexers IndexerDataInterface[] */
        $filteredIndexers = array_filter($indexers, [$this, '_isIndexer']);
        $this->_indexerManagement->execute($filteredIndexers);
    }

    /**
     * Check if is valid indexer
     * @param $indexer
     * @return bool
     */
    private function _isIndexer($indexer): bool {
        return $indexer instanceof IndexerDataInterface;
    }
}
