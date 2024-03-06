<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface SellerFeedSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Netsteps\Seller\Api\Data\SellerFeedInterface[]
     */
    public function getItems();

    /**
     * @param \Netsteps\Seller\Api\Data\SellerFeedInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
