<?php
/**
 * AddLowestSellerInfo
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Marketplace\StockManagement;

use Netsteps\MarketplaceSales\Api\ProductManagementInterface as ProductManagement;
use Magento\Framework\DB\Adapter\AdapterInterface as Connection;
use Magento\Framework\App\ResourceConnection;
use Netsteps\MarketplaceSales\Traits\DataCastTrait;

/**
 * Class AddLowestSellerInfo
 * @package Netsteps\MarketplaceSales\Plugin\Marketplace\StockManagement
 */
class AddLowestSellerInfo
{
    use DataCastTrait;

    /**
     * @var ProductManagement
     */
    private ProductManagement $_productManagement;

    /**
     * @var Connection
     */
    private Connection $_connection;

    /**
     * @param ProductManagement $productManagement
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ProductManagement $productManagement,
        ResourceConnection $resourceConnection
    )
    {
        $this->_productManagement = $productManagement;
        $this->_connection = $resourceConnection->getConnection();
    }

    /**
     * @param \Netsteps\Marketplace\Api\StockManagementInterface $stockManagement
     * @param $result
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterface[] $sourceItems
     * @return void
     */
    public function afterSaveItems(
        \Netsteps\Marketplace\Api\StockManagementInterface $stockManagement,
        $result,
        array $sourceItems
    ): void {
        $productIds = $this->getProductIds($sourceItems);

        if (empty($productIds)){
            return;
        }

        try {
            $this->_productManagement->updateProductsData($productIds);
        } catch (\Throwable $t) {
            //DO nothing
        }
    }

    /**
     * Get product ids from source items
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterface[] $sourceItems
     * @return int[]
     */
    private function getProductIds(array $sourceItems): array {
        $skus = array_unique(array_map([$this, 'extractSku'], $sourceItems));

        if (empty($skus)) {
            return [];
        }

        $select = $this->_connection->select()
            ->from(
                ['e' => $this->_connection->getTableName('catalog_product_entity')],
                'entity_id'
            )
            ->where('e.sku IN (?)', $skus);

        $ids = $this->_connection->fetchCol($select);
        return array_map([$this, 'castStringToInt'], $ids);
    }

    /**
     * Extract sku from source item
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterface $item
     * @return string
     */
    private function extractSku(\Magento\InventoryApi\Api\Data\SourceItemInterface $item): string {
        return $item->getSku() ?? '';
    }
}
