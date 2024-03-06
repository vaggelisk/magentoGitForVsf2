<?php
/**
 * Collection
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\ResourceModel\OrderRelation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

use Netsteps\MarketplaceSales\Model\ResourceModel\OrderRelation as ResourceModel;
use Netsteps\MarketplaceSales\Model\OrderRelation as Model;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface as ModelInterface;

/**
 * Class Collection
 * @package Netsteps\MarketplaceSales\Model\ResourceModel\OrderRelation
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource model and model for collection
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    /**
     * Add is main order filter to collection
     * @return $this
     */
    public function addIsMainFilter(): Collection {
        if (!$this->hasFlag('is_main_filter')){
            $this->addFieldToFilter(ModelInterface::IS_MAIN_ORDER, true);
            $this->setFlag('is_main_filter', true);
        }

        return $this;
    }

    /**
     * Add processed status filter to collection
     * @return $this
     */
    public function addProcessedFilter(bool $status = true): Collection {
        if (!$this->hasFlag('processed_status_filter')){
            $this->addFieldToFilter(ModelInterface::IS_PROCESSED, $status);
            $this->setFlag('processed_status_filter', true);
        }

        return $this;
    }

    /**
     * Add is child filter to collection
     * @return $this
     */
    public function addIsChildFilter(): Collection {
        if (!$this->hasFlag('is_child_filter')){
            $this->addFieldToFilter(ModelInterface::IS_MAIN_ORDER, 0);
            $this->setFlag('is_child_filter', true);
        }

        return $this;
    }

    /**
     * Add parent order filter
     * @param int|array $id
     * @return Collection
     */
    public function addParentOrderFilter(int|array $id): Collection {
        if (!$this->hasFlag('has_parent_order_filter')){
            if (!is_array($id)) {
                $id = [$id];
            }

            $this->addFieldToFilter(ModelInterface::PARENT_ORDER_ID, ['in' => $id]);
            $this->setFlag('has_parent_order_filter', true);
        }

        return $this;
    }
}
