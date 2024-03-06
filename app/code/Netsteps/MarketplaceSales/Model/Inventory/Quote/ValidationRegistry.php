<?php
/**
 * ValidationRegistry
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Inventory\Quote;

/**
 * Class ValidationRegistry
 * @package Netsteps\MarketplaceSales\Model\Inventory\Quote
 */
class ValidationRegistry
{
    /**
     * @var array
     */
    private static array $ignoredQuotes = [];

    /**
     * @param int $quoteId
     * @return void
     */
    public function registerIgnore(int $quoteId): void {
        if (!$this->isIgnored($quoteId)){
            self::$ignoredQuotes[] = $quoteId;
        }
    }

    /**
     * Check is quote id is ignored
     * @param int $quoteId
     * @return bool
     */
    public function isIgnored(int $quoteId): bool {
       return in_array($quoteId, self::$ignoredQuotes);
    }

    /**
     * Clear registry
     * @param array $quoteIds
     * @return void
     */
    public function clear(array $quoteIds = []): void {
        self::$ignoredQuotes = [];
    }
}
