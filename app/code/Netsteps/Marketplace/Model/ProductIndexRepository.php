<?php
/**
 * ProductIndexRepository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Netsteps\Marketplace\Api\ProductIndexRepositoryInterface;
use Netsteps\Marketplace\Api\Data\MerchantDataInterface as MerchantData;
use Netsteps\Marketplace\Api\Data\MerchantDataInterfaceFactory as MerchantDataFactory;
use Magento\Framework\App\ResourceConnection as ResourceConnection;

/**
 * Class ProductIndexRepository
 * @package Netsteps\Marketplace\Model
 */
class ProductIndexRepository implements ProductIndexRepositoryInterface
{
    /**
     * @var int
     */
    private int $_batchSize = 5000;

    /**
     * @var MerchantDataFactory
     */
    private MerchantDataFactory $_merchantDataFactory;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @var \Netsteps\Marketplace\Model\Select\ModifierInterface[]
     */
    private array $_modifiers;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MerchantDataFactory $merchantDataInterfaceFactory
     * @param \Netsteps\Marketplace\Model\Select\ModifierInterface[] $modifiers
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MerchantDataFactory $merchantDataInterfaceFactory,
        array $modifiers = []
    ) {
       $this->_merchantDataFactory = $merchantDataInterfaceFactory;
       $this->_connection = $resourceConnection->getConnection();
       $this->_modifiers = $modifiers;
    }

    /**
     * @inheritDoc
     */
    public function getBestSellerDataByProductId(int $productId, ?int $minQty = null): ?\Netsteps\Marketplace\Api\Data\MerchantDataInterface
    {
        $select = $this->initSelect();
        $this->addStockDataToSelect($select);
        $this->addFinalPriceToSelect($select);
        $this->addOrder($select);
        $select->where('main_table.product_id = ?', $productId);

        foreach ($this->_modifiers as $modifier) {
            $modifier->modify($select);
        }

        if ($minQty > 0) {
            $select->where('stock.quantity >= ?', $minQty);
        }

        $data = $this->_connection->fetchRow($select);

        if (!is_array($data) || empty($data)) {
            return null;
        }

        return $this->_merchantDataFactory->create(['data' => $data]);
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    private function initSelect(): \Magento\Framework\DB\Select {
        return $this->_connection->select()
            ->from(
                ['main_table' => $this->_connection->getTableName(MerchantData::TABLE)]
            );
    }

    /**
     * Add stock data to select
     * @param \Magento\Framework\DB\Select $select
     * @return void
     */
    private function addStockDataToSelect(\Magento\Framework\DB\Select $select): void {
        $select->join(
            ['cpe' => $this->_connection->getTableName('catalog_product_entity')],
            'cpe.entity_id = main_table.product_id',
            []
        )->join(
            ['se' => $this->_connection->getTableName('seller_entity')],
            'se.entity_id = main_table.seller_id',
            []
        )->join(
            ['stock' => $this->_connection->getTableName('inventory_source_item')],
            'stock.sku = cpe.sku AND stock.source_code = se.source_code',
            ['quantity', 'source_code']
        )
            ->where('se.status = ?', 1);
    }

    /**
     * Add final price
     * @param \Magento\Framework\DB\Select $select
     * @return void
     */
    private function addFinalPriceToSelect(\Magento\Framework\DB\Select $select): void {
        $select->columns([
            'final_price' => new \Zend_Db_Expr(
                'IF(main_table.special_price IS NOT NULL AND main_table.special_price < main_table.price, main_table.special_price, main_table.price)'
            )
        ]);
    }

    /**
     * Add sorting
     * @param \Magento\Framework\DB\Select $select
     * @return void
     */
    private function addOrder(\Magento\Framework\DB\Select $select): void {
        $select->order(['final_price ASC', 'stock.quantity DESC']);
    }

    /**
     * @inheritDoc
     */
    public function getAllProductSellerData(int $productId): array
    {
        $select = $this->initSelect();
        $this->addStockDataToSelect($select);
        $this->addFinalPriceToSelect($select);
        $this->addOrder($select);
        $select->where('main_table.product_id = ?', $productId);

        $rawData = $this->_connection->fetchAll($select);
        $result = [];

        foreach ($rawData as $data) {
            $result[] = $this->_merchantDataFactory->create(['data' => $data]);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getProductsBestOfferSeller(array $productIds = []): \Traversable
    {
        foreach ($this->getProductBatches($productIds) as $batch) {
            if (empty($batch)){
                yield null;
            }

            $initSelect = $this->initSelect();
            $this->addStockDataToSelect($initSelect);
            $this->addFinalPriceToSelect($initSelect);

            $initSelect->order('cpe.entity_id');
            $this->addOrder($initSelect);

            $initSelect->where('cpe.entity_id IN (?)', $batch);

            /** Use session variables to emulate ROW_NUMBER over partition functionality of MySQL >= 8 */
            $select = $this->_connection->select()->from(
                ['mt' => $initSelect],
                '*'
            )->joinCross(
                ['tmp' => new \Zend_Db_Expr('(SELECT @row_number:=0, @prev_id := NULL)')],
                []
            )->columns([
                'row_num' => new \Zend_Db_Expr('(@row_number := IF(@prev_id IS NULL OR @prev_id != mt.product_id, 1, @row_number + 1))'),
                'current_product_id' => new \Zend_Db_Expr('(@prev_id := mt.product_id)')
            ])->having('row_num = ?', 1);

            $rawData = $this->_connection->fetchAll($select);

            $data = [];
            foreach ($rawData as $rawDatum) {
                unset($rawDatum['row_num'], $rawDatum['current_product_id']);
                $data[] = $this->_merchantDataFactory->create(['data' => $rawDatum]);
            }

            $diff = array_diff($batch, array_column($rawData, 'product_id'));
            foreach ($diff as $productIdWithoutData) {
                /** @var  $merchantData MerchantData */
                $merchantData = [MerchantData::PRODUCT_ID => $productIdWithoutData, 'is_delete' => true];
                $data[] = $this->_merchantDataFactory->create(['data' => $merchantData]);
            }

            yield $data;
        }
    }

    /**
     * Get product batches
     * @param array $productIds
     * @return \Traversable
     */
    private function getProductBatches(array $productIds = []): \Traversable {
        if (!empty($productIds) && count($productIds) <= $this->_batchSize){
            yield  $productIds;
        } else if (!empty($productIds)) {
           foreach(array_chunk($productIds, $this->_batchSize) as $batch) {
               yield $batch;
           }
        } else {
            $size = $this->_getCountOfProductIds();
            $offsets = $size >= $this->_batchSize ? range(0, $size, $this->_batchSize) : [0];

            foreach ($offsets as $offset) {
                yield $this->_getProductIdsInRange($offset);
            }
        }
    }

    /**
     * Get distinct counter of products
     * @return int
     */
    private function _getCountOfProductIds(): int {
        $countSelect = $this->initSelect()->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['count' => new \Zend_Db_Expr('COUNT(DISTINCT main_table.product_id)')]);

        return (int)$this->_connection->fetchOne($countSelect);
    }

    /**
     * Get product ids in range
     * @param int $offset
     * @return array
     */
    private function _getProductIdsInRange(int $offset): array {
        $select = $this->initSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns('product_id')
            ->limit($this->_batchSize, $offset)
            ->order('main_table.product_id ASC')
            ->distinct(true);

        return $this->_connection->fetchCol($select);
    }
}
