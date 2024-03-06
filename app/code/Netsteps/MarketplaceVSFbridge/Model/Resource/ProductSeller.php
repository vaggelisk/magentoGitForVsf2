<?php
/**
 * ProductSeller
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Model\Resource;

use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * Class ProductSeller
 * @package Netsteps\MarketplaceVSFbridge\Model\Resource
 */
class ProductSeller
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resource;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @param ResourceConnection $resourceConnection
     * @param EventManager $eventManager
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        EventManager $eventManager
    )
    {
        $this->resource = $resourceConnection;
        $this->_eventManager = $eventManager;
    }

    /**
     * Load raw data as array
     * @param array $productIds
     * @param int|null $sellerId
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function loadData(array $productIds = [], ?int $sellerId = null, ?int $page = null, ?int $limit = null): array {
        $select = $this->getSelect();

        if (!empty($productIds)) {
            $select->where('cpe.entity_id IN (?)', $productIds);
        }

        if (!is_null($sellerId) && $sellerId > 0){
            $select->where('seller.entity_id = ?', $sellerId);
        }

        /**
         * Dispatch event before index data
         */
        $this->_eventManager->dispatch(
            'marketplace_index_seller_data_before',
            ['select' => $select, 'product_ids' => $productIds, 'seller_id' => $sellerId]
        );

        if ($page > 0 && $limit > 1){
            $select->limitPage($page, $limit);

        }
        return $this->getConnection()->fetchAll($select);
    }


    /**
     * @return \Magento\Framework\DB\Select
     */
    private function getSelect(): \Magento\Framework\DB\Select {
        $connection = $this->getConnection();

        return $connection->select()
            ->from(
                ['main_table' => $connection->getTableName('seller_product_index')],
                ['price', 'special_price', 'delivery_id']
            )
            ->join(
                ['seller' => $connection->getTableName('seller_entity')],
                'main_table.seller_id = seller.entity_id',
                ['seller_id' => 'entity_id', 'seller_name' => 'name', 'seller_group' => 'group']
            )
            ->join(
                ['cpe' => $connection->getTableName('catalog_product_entity')],
                'main_table.product_id = cpe.entity_id',
                ['product_id' => 'cpe.entity_id']
            )
            ->join(
                ['msi' => $connection->getTableName('inventory_source_item')],
                'cpe.sku = msi.sku AND seller.source_code = msi.source_code',
                ['qty' => 'quantity', 'is_in_stock' => 'status']
            )
            ->joinLeft(
                ['cpsl' => $connection->getTableName('catalog_product_super_link')],
                'cpe.entity_id = cpsl.product_id',
                ['parent_id']
            )
            ->columns([
                'final_price' => new \Zend_Db_Expr('IF(special_price IS NOT NULL AND special_price < price, special_price, price)')
            ]);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface {
        return $this->resource->getConnection();
    }
}
