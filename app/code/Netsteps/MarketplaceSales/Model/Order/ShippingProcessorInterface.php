<?php
/**
 * ShippingPrepareProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

/**
 * Interface ShippingPrepareProcessorInterface
 * @package Netsteps\MarketplaceSales\Model\Order
 */
interface ShippingProcessorInterface
{
    /**
     * Prepare new shipping address for given quote based on date from previous quote
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address $existingAddress
     * @param string|null $shippingMethodCode
     * @param string|null $shippingCarrierCode
     * @return void
     */
    public function prepare(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address $existingAddress,
        ?string $shippingMethodCode = null,
        ?string $shippingCarrierCode = null,
    ): void;
}
