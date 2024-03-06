<?php
/**
 * MethodMapperInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Shipping;

/**
 * Interface MethodMapperInterface
 * @package Netsteps\MarketplaceSales\Model\Order\Shipping
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
     * Get original methods tha
     * @param string $method
     * @return string[]
     */
    public function match(string $method): array;
}
