<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface SellerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Netsteps\Seller\Api\Data\SellerInterface[]
     */
    public function getItems();

    /**
     * @param \Netsteps\Seller\Api\Data\SellerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
