<?php
/**
 * MethodMapperInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Payment;

/**
 * Interface MethodMapperInterface
 * @package Netsteps\MarketplaceSales\Model\Order\Payment
 */
interface MethodMapperInterface
{
    /**
     * Get mapped payment method code based on original method
     * @param string $method
     * @return string
     */
    public function getMappedMethod(string $method): string;

    /**
     * Get all map
     * @return array
     */
    public function getMap(): array;

    /**
     * Get all invoiced methods
     * @return string[]
     */
    public function getInvoicedMethods(): array;

    /**
     * Check if given payment method code need to be invoiced after order
     * creation.
     * @param string $method
     * @return bool
     */
    public function needInvoice(string $method): bool;

    /**
     * Get original method
     * @param string $method
     * @return string
     */
    public function getOriginalMethod(string $method): string;
}
