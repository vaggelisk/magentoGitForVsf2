<?php
/**
 * Collection
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\ResourceModel\Product\History;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netsteps\Marketplace\Api\Data\ProductHistoryInterface;
use Netsteps\Marketplace\Model\ResourceModel\Product\History as ResourceModel;
use Netsteps\Marketplace\Model\Product\History as Model;

/**
 * Class Collection
 * @package Netsteps\Marketplace\Model\ResourceModel\Product\History
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize collection's model and resource
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
        parent::_construct();
    }

    /**
     * Add sku filter to collection
     * @param mixed $sku
     * @return $this
     */
    public function addSkuFilter(mixed $sku): self {

        if (!$this->hasFlag('sku_filter')){
            if (!is_array($sku)){
                $sku = [$sku];
            }

            $this->addFieldToFilter(ProductHistoryInterface::ID, $sku);
        }

        return $this;
    }
}
