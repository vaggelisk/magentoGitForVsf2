<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Api\SearchResults;
use Netsteps\Seller\Api\Data\SellerSearchResultsInterface;

class SellerSearchResults extends SearchResults implements SellerSearchResultsInterface
{
}
