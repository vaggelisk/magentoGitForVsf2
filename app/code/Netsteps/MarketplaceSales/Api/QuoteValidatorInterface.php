<?php
/**
 * QuoteValidatorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api;


/**
 * Interface QuoteValidatorInterface
 * @package Netsteps\MarketplaceSales\Api
 */
interface QuoteValidatorInterface
{
    /**
     * Validate quote for items data given. Items data variable $itemsData
     * should be in format
     * array(
     *      item_id => array(
     *          'qty' => <item_qty_requested>
     *      ),
     *      ....
     * )
     *
     * The return value is a string array with the name of the products that
     * does not have a valid requested quantity
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param array $itemsData
     * @return void
     * @throws \Netsteps\MarketplaceSales\Exception\OutOfStockException
     */
    public function validate(
        \Magento\Quote\Api\Data\CartInterface $quote,
        array $itemsData
    ): void;
}
