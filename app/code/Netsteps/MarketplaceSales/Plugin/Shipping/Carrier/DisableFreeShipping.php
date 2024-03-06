<?php
/**
 * DisableFreeShipping
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Shipping\Carrier;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class DisableFreeShipping
 * @package Netsteps\MarketplaceSales\Plugin\Shipping\Carrier
 */
class DisableFreeShipping
{
    /**
     * Disable free shipping method in web api rest area
     * @param \Magento\OfflineShipping\Model\Carrier\Freeshipping $freeshipping
     * @param callable $proceed
     * @param RateRequest $request
     * @return bool
     */
    public function aroundCollectRates(
        \Magento\OfflineShipping\Model\Carrier\Freeshipping $freeshipping,
        callable $proceed,
        RateRequest $request
    ): bool {
        return false;
    }
}
