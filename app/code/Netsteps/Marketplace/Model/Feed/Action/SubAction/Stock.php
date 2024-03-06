<?php
/**
 * Stock
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action\SubAction;

use Netsteps\Marketplace\Model\Feed\Action\SubActionInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Marketplace\Api\StockManagementInterface as StockManagement;

/**
 * Class Stock
 * @package Netsteps\Marketplace\Model\Feed\Action\SubAction
 */
class Stock implements SubActionInterface
{

    const ACTION_CODE = 'stock';

    const DEFAULT_BATCH_SIZE = 5000;

    /**
     * @var StockManagement
     */
    private StockManagement $_stockManagement;

    /**
     * @var int
     */
    private int $batchSize;

    /**
     * @param StockManagement $stockManagement
     * @param int $batchSize
     */
    public function __construct(StockManagement $stockManagement, int $batchSize = self::DEFAULT_BATCH_SIZE)
    {
        $this->_stockManagement = $stockManagement;
        $this->batchSize = (int)max($batchSize, 500);
    }

    /**
     * @inheritDoc
     */
    public function process(array $data, SellerInterface $seller): void
    {
        if (empty($data) || !$seller->getSourceCode()){
            return;
        }

        foreach ($this->getStockDataToSave($data, $seller->getSourceCode()) as $items) {
            $this->_stockManagement->saveItems($items);
        }
    }

    /**
     * @param ItemInterface[] $items
     * @param string $sourceCode
     * @return \Traversable
     */
    private function getStockDataToSave(array $items, string $sourceCode): \Traversable {
        $batches = array_chunk($items, $this->batchSize);

        foreach ($batches as $batch) {
            $data = [];

            /** @var  $item ItemInterface */
            foreach ($batch as $item) {
                $source = $item->getStockSourceCode() ?? $sourceCode;

                $data[] = $this->_stockManagement->createSourceItem(
                    $item->getSku(),
                    $item->getStock(),
                    $item->getIsInStock() === ItemInterface::IN_STOCK_FLAG,
                    $source
                );

                if ($source !== 'default') {
                    $data[] = $this->_stockManagement->createSourceItem($item->getSku(), 1, 1, 'default');
                }
            }

            yield $data;
        }
    }
}
