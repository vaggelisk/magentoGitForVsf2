<?php
/**
 * ReindexAfterPlace
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Order;

use Netsteps\MarketplaceSales\Model\Product\Metadata;
use Netsteps\Reindex\Model\Indexer\Factory as IndexerDataFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Netsteps\MarketplaceSales\Api\ProductManagementInterface as ProductManagement;

/**
 * Class ReindexAfterPlace
 * @package Netsteps\MarketplaceSales\Plugin\Order
 */
class ReindexAfterPlace
{
    /**
     * @var IndexerDataFactory
     */
    private IndexerDataFactory $_indexerDataFactory;

    /**
     * @var ProductManagement
     */
    private ProductManagement $_productManagement;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @param IndexerDataFactory $indexerDataFactory
     * @param ProductManagement $productManagement
     * @param EventManager $eventManager
     */
    public function __construct(
        IndexerDataFactory $indexerDataFactory,
        ProductManagement $productManagement,
        EventManager $eventManager)
    {
        $this->_indexerDataFactory = $indexerDataFactory;
        $this->_productManagement = $productManagement;
        $this->_eventManager = $eventManager;
    }

    /**
     * Trigger reindex and update marketplace seller data
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Api\Data\OrderInterface $orderResult
     * @param \Magento\Sales\Api\Data\OrderInterface $orderArgument
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterPlace(
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Api\Data\OrderInterface $orderResult,
        \Magento\Sales\Api\Data\OrderInterface $orderArgument
    ): \Magento\Sales\Api\Data\OrderInterface {

        try {
            $productIds = $this->_getProductIds($orderResult);
            if (!empty($productIds)) {
                $this->_productManagement->updateProductsData($productIds);
                $indexers[] = $this->_indexerDataFactory->create('vsbridge_product_indexer', $productIds);
                $indexers[] = $this->_indexerDataFactory->create('marketplace_seller_product', $productIds);
                $this->_eventManager->dispatch('ns_trigger_reindex', ['indexers' => $indexers]);
            }
        } catch (\Exception $e) {
            /** Do nothing in case of exception at the moment */
        }

        return $orderResult;
    }

    /**
     * Get product ids from order to trigger reindex
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int[]
     */
    private function _getProductIds(\Magento\Sales\Api\Data\OrderInterface $order): array {
        $ids = [];

        $indexedTypes = Metadata::getProductIndexedTypes();
        foreach ($order->getItems() as $item) {
            if (!in_array($item->getProductType(), $indexedTypes)){
                continue;
            }

            $ids[] = (int)$item->getProductId();
        }

        return $ids;
    }
}
