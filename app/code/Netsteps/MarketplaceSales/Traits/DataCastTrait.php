<?php
/**
 * DataCastTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Traits;

/**
 * Trait DataCast trait
 * @package Netsteps\MarketplaceSales\Traits
 */
trait DataCastTrait
{
    /**
     * Normalize string to int
     * @param string $value
     * @return int
     */
    protected function castStringToInt(string $value): int {
        return (int)trim($value);
    }
}
