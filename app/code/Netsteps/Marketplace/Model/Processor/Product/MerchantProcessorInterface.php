<?php
/**
 * MerchantProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Processor\Product;

use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Processor\ResultInterface;

/**
 * Interface MerchantProcessorInterface
 * @package Netsteps\Marketplace\Model\Processor\Product
 */
interface MerchantProcessorInterface
{
    /**
     * Process xml items
     * @param ItemInterface[] $items
     * @param int $sellerId
     * @return ResultInterface
     */
    public function processItems(array $items, int $sellerId): ResultInterface;

    /**
     * Process a product
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function processProduct(\Magento\Catalog\Model\Product $product): void;

    /**
     * Process single item
     * @param ItemInterface $item
     * @param int $sellerId
     * @param int $productId
     * @return void
     */
    public function processItem(ItemInterface $item, int $sellerId, int $productId): void;
}
