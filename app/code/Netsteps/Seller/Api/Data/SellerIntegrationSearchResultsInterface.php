<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


interface SellerIntegrationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Netsteps\Seller\Api\Data\SellerIntegrationInterface[]
     */
    public function getItems();

    /**
     * @param \Netsteps\Seller\Api\Data\SellerIntegrationInterface[] $items
     * @return void
     */
    public function setItems(array $items);

}
