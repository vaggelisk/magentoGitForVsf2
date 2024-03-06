<?php
/**
 * PaymentProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

/**
 * Interface PaymentProcessorInterface
 * @package Netsteps\MarketplaceSales\Model\Order
 */
interface PaymentProcessorInterface
{
    /**
     * Prepare payment for quote. Return true if new order need to be invoiced immediately
     * and false in any other case.
     *
     * @param \Magento\Quote\Model\Quote $cart
     * @param \Magento\Quote\Api\Data\AddressInterface $existingAddress
     * @param string $paymentMethodCode
     * @return bool
     */
    public function preparePayment(
        \Magento\Quote\Model\Quote $cart,
        \Magento\Quote\Api\Data\AddressInterface $existingAddress,
        string $paymentMethodCode
    ): bool;
}
