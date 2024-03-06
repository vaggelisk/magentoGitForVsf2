<?php
/**
 * AfterProcessFeed
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Marketplace;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\MarketplaceSales\Api\ProductManagementInterface as ProductManagement;
use Netsteps\MarketplaceSales\Model\Product\Metadata as ProductMetadata;

/**
 * Class AfterProcessFeed
 * @package Netsteps\MarketplaceSales\Observer\Marketplace
 */
class AfterProcessFeed implements ObserverInterface
{
    /**
     * @var ProductMetadata
     */
    private ProductMetadata $_productMetadata;

    /**
     * @var ProductManagement
     */
    private ProductManagement $_productManagement;

    /**
     * @param ProductManagement $productManagement
     * @param ProductMetadata $productMetadata
     */
    public function __construct(
        ProductManagement $productManagement,
        ProductMetadata $productMetadata
    )
    {
        $this->_productManagement = $productManagement;
        $this->_productMetadata = $productMetadata;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $items = $observer->getEvent()->getItems();
        if (!is_array($items) || empty($items)){
            return;
        }

        $skus = $this->exportSkusFromItems($items);
        if (empty($skus)){
            return;
        }

        $productIds = $this->_productMetadata->getProductIds($skus, true);
        if (empty($productIds)){
            return;
        }

        $this->_productManagement->updateProductsData($productIds);
    }

    /**
     * Export skus from items
     * @param array $items
     * @return array
     */
    private function exportSkusFromItems(array $items): array {
        $items = array_filter($items, [$this, 'isFeedItem']);

        return array_map(function (\Netsteps\Marketplace\Model\Feed\ItemInterface $item) {
            return $item->getSku();
        }, $items);
    }

    /**
     * Check if is feed item
     * @param object $item
     * @return bool
     */
    private function isFeedItem(object $item): bool {
        return $item instanceof \Netsteps\Marketplace\Model\Feed\ItemInterface;
    }
}
