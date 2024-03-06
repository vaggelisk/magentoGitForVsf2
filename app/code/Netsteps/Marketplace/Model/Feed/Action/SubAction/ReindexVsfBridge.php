<?php

namespace Netsteps\Marketplace\Model\Feed\Action\SubAction;

use Divante\VsbridgeIndexerCore\Indexer\Action\AbstractAction;
use Divante\VsbridgeIndexerCore\Indexer\Action\ActionFactory;
use Netsteps\Marketplace\Model\Feed\Action\SubActionInterface;
use Netsteps\Seller\Api\Data\SellerInterface;

class ReindexVsfBridge implements SubActionInterface
{
    const ACTION_CODE = 'reindex_vsf_bridge';

    /**
     * @var \Divante\VsbridgeIndexerCore\Indexer\Action\AbstractAction
     */
    private AbstractAction $indexer;

    /**
     * @param \Divante\VsbridgeIndexerCore\Indexer\Action\ActionFactory $indexerFactory
     */
    public function __construct(
        ActionFactory $indexerFactory
    )
    {
        $this->indexer = $indexerFactory->create('rows', 'product');
    }

    /**
     * @inheritDoc
     */
    public function process(array $data, SellerInterface $seller): void
    {
        $logger = new \Zend_Log(
            new \Zend_Log_Writer_Stream(BP . '/var/log/vs_bridge_feed_reindex')
        );

        $logger->info("Reindexing after feed import\n");

        if(empty($data)) return;

        $ids = [];

        foreach ($data as $product){
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $ids[] = $product->getId();
        }

        $ids = array_unique($ids);

        $logger->info("------------- IDS --------------");
        $logger->info(json_encode($ids));

        $this->indexer->execute($ids);
    }
}
