<?php
/**
 * QuoteManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

/**
 * Interface QuoteManagementInterface
 * @package Netsteps\MarketplaceSales\Model\Order
 */
interface SplitQuoteManagementInterface
{
    /**
     * Prepare a quote for a new split order
     * @param \Magento\Quote\Model\Quote $quote
     * @return void
     */
    public function prepareQuote(\Magento\Quote\Model\Quote $quote): void;

    /**
     * Create new quotes based on registered items.
     * @return void
     */
    public function resolve(): void;
}
