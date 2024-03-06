<?php
/**
 * ManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Item;

/**
 * Interface ManagementInterface
 * @package Netsteps\Marketplace\Model\Product\Item
 */
interface ManagementInterface
{
    /**
     * Update product base on item exported from given item
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @param string $sku
     * @param array $additionalData
     * @param int $storeId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function update(
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        string $sku,
        array $additionalData = [],
        int $storeId = 0
    ): \Magento\Catalog\Api\Data\ProductInterface;

    /**
     * Add children to configurable product
     * @param string $configurableSku
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $children
     * @return void
     */
    public function addConfigurableChildren(string $configurableSku, array $children): void;
}
