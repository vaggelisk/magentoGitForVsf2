<?php
/**
 * ValidateQuantityBeforeSet
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Quote\Item;

use Netsteps\MarketplaceSales\Exception\OutOfStockException;
use Netsteps\MarketplaceSales\Api\QuoteValidatorInterface as QuoteValidator;

/**
 * Class ValidateQuantityBeforeSet
 * @package Netsteps\MarketplaceSales\Plugin\Quote\Item
 */
class ValidateQuantityBeforeSet
{
    /**
     * @var QuoteValidator
     */
    private QuoteValidator $_quoteValidator;

    /**
     * @param QuoteValidator $quoteValidator
     */
    public function __construct(QuoteValidator $quoteValidator)
    {
        $this->_quoteValidator = $quoteValidator;
    }

    /**
     * @param \Magento\Checkout\Model\Cart $cart
     * @param array $data
     * @return void
     * @throws OutOfStockException
     */
    public function beforeUpdateItems(
        \Magento\Checkout\Model\Cart $cart,
        array $data
    ): void
    {
        $quote = $cart->getQuote();
        $this->_quoteValidator->validate($quote, $data);
    }
}
