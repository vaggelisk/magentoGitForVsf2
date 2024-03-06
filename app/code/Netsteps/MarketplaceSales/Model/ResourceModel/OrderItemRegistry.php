<?php
/**
 * OrderItemRegistry
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface as Model;

/**
 * Class OrderItemRegistry
 * @package Netsteps\MarketplaceSales\Model\ResourceModel
 */
class OrderItemRegistry extends AbstractDb
{
    protected $_idFieldName = Model::ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Model::TABLE, Model::ID);
    }
}
