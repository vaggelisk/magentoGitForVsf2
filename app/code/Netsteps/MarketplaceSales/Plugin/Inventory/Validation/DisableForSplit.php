<?php
/**
 * DisableForSplit
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Inventory\Validation;

use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Netsteps\MarketplaceSales\Model\Inventory\Quote\ValidationRegistry;

/**
 * Class DisableForSplit
 * @package Netsteps\MarketplaceSales\Plugin\Inventory\Validation
 */
class DisableForSplit
{
    /**
     * @var ValidationRegistry
     */
    private ValidationRegistry $_validationRegistry;

    /**
     * @param ValidationRegistry $validationRegistry
     */
    public function __construct(ValidationRegistry $validationRegistry)
    {
        $this->_validationRegistry = $validationRegistry;
    }

    /**
     * Disable quantity validation in cron area for split order process
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $validator
     * @param callable $proceed
     * @param Observer $observer
     * @return void
     */
    public function aroundValidate(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $validator,
        callable $proceed,
        Observer $observer
    ): void {
        $item = $observer->getEvent()->getItem();

        if ($item instanceof QuoteItem) {
            $quote = $item->getQuote();

            if ($quote && $quote->getEntityId() && $this->_validationRegistry->isIgnored($quote->getEntityId())){
                return;
            }
        }

        $proceed($observer);
    }
}
