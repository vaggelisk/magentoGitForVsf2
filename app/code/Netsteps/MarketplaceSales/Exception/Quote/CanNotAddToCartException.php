<?php
/**
 * CanNotAddToCartException
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Exception\Quote;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class CanNotAddToCartException
 * @package Netsteps\MarketplaceSales\Exception\Quote
 */
class CanNotAddToCartException extends LocalizedException
{
    protected $code = 520;
}
