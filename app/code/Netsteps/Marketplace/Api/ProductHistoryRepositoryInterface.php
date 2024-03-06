<?php
/**
 * ProductHistoryRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api;

/**
 * Interface ProductHistoryRepositoryInterface
 * @package Netsteps\Marketplace\Api
 */
interface ProductHistoryRepositoryInterface
{
    /**
     * Save a product history item
     * @param \Netsteps\Marketplace\Api\Data\ProductHistoryInterface $productHistory
     * @return \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
     */
    public function save(\Netsteps\Marketplace\Api\Data\ProductHistoryInterface $productHistory): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface;

    /**
     * Get history by sku
     * @param string $sku
     * @param bool $force
     * @return \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $sku, bool $force = false): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface;

    /**
     * Delete history by sku
     * @param string $sku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     * @return bool
     */
    public function deleteBySku(string $sku): bool;

    /**
     * Get all history data
     * @return array
     */
    public function getAllHistoryData(): array;

    /**
     * Create a new empty item
     * @return \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
     */
    public function createNewItem(): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface;

    /**
     * Create a new history item
     * @param string $sku
     * @param array $data
     * @return \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
     */
    public function createHistoryItem(string $sku, array $data): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface;

    /**
     * Get a history collection
     * @param array $skus
     * @return \Netsteps\Marketplace\Model\ResourceModel\Product\History\Collection
     */
    public function getHistory(array $skus = []): \Netsteps\Marketplace\Model\ResourceModel\Product\History\Collection;

    /**
     * Check if given sku is needed update
     * @param string $sku
     * @param array $data
     * @return bool
     */
    public function isNeededUpdate(string $sku, array $data): bool;

    /**
     * Update version
     * @param string $sku
     * @param array $data
     * @return bool
     */
    public function updateVersion(string $sku, array $data): bool;

    /**
     * Check if product already exists
     * @param string $sku
     * @return bool
     */
    public function isProductExists(string $sku): bool;
}
