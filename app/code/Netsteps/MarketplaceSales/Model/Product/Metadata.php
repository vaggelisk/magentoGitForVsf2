<?php
/**
 * Metadata
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Product;

use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Netsteps\MarketplaceSales\Traits\DataCastTrait;

/**
 * Class Metadata
 * @package Netsteps\MarketplaceSales\Model\Product
 */
class Metadata
{
    use DataCastTrait;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->_connection = $resourceConnection->getConnection();
    }

    /**
     * Get product ids from sku
     * @param string[] $skus
     * @param bool $includeSimple
     * @return int[]
     */
    public function getProductIds(array $skus, bool $includeSimple = false): array {
        if (empty($skus)){
            return [];
        }

        $select = $this->_connection->select()->from(
            ['e' => $this->_connection->getTableName('catalog_product_entity')],
            ['entity_id']
        )->where('e.sku IN (?)', $skus);

        if ($includeSimple) {
            $select->joinLeft(
              ['sl' => $this->_connection->getTableName('catalog_product_super_link')],
              'e.entity_id = sl.parent_id',
              []
            );

            $select->reset(\Zend_Db_Select::COLUMNS);
            $select->columns([
                'entity_id' => new \Zend_Db_Expr('IF(sl.product_id IS NULL, e.entity_id, sl.product_id)')
            ]);
        }

        return array_map(
            [$this, 'castStringToInt'],
            array_unique($this->_connection->fetchCol($select))
        );
    }

    /**
     * Get product indexed types
     * @return array
     */
    public static function getProductIndexedTypes(): array {
        return [
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
        ];
    }
}
