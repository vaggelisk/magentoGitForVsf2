<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */
namespace Netsteps\Seller\Traits;

use Magento\Framework\Api\Search\FilterGroup;

trait RepositoryTrait
{
    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $configCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection($filterGroup, $collection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }
}
