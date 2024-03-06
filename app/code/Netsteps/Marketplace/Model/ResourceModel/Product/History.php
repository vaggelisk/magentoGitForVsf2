<?php
/**
 * History
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\Marketplace\Api\Data\ProductHistoryInterface as Model;

/**
 * Class History
 * @package Netsteps\Marketplace\Model\ResourceModel\Product
 */
class History extends AbstractDb
{
    protected $_idFieldName = Model::ID;

    protected $_isPkAutoIncrement = false;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Model::TABLE, Model::ID);
    }
}
