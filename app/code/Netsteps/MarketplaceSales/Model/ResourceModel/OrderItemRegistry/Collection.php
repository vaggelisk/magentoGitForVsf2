<?php
/**
 * Collection
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\ResourceModel\OrderItemRegistry;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderItemRegistry as ResourceModel;
use Netsteps\MarketplaceSales\Model\OrderItemRegistry as Model;

/**
 * Class Collection
 * @package Netsteps\MarketplaceSales\Model\ResourceModel\OrderItemRegistry
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize model and resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
