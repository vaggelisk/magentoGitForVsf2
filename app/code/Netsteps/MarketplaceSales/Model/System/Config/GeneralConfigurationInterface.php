<?php
/**
 * GeneralConfigurationInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\System\Config;

/**
 * Interface GeneralConfigurationInterface
 * @package Netsteps\Marketplace\Model\System\Config
 */
interface GeneralConfigurationInterface
{
    const FIELD_DISABLE_SOURCE_SELECTION = 'disable_source_selection';
    const FIELD_DISABLE_ORDER_TYPES_DEDUCTION = 'disable_stock_deduction_order_types';
    const FIELD_DISABLE_ORDER_TYPES_REVERT = 'disable_stock_revert_order_types';

    /**
     * Check if is source selection disabled
     * @return bool
     */
    public function isSourceSelectionDisabled(): bool;

    /**
     * Get an array of order types that stock deduction during order creation is disabled
     * @return array
     */
    public function getDisabledOrderTypesForStockDeduction(): array;

    /**
     * Get an array of order types that stock revert during an order cancelation/refund is disabled
     * @return array
     */
    public function getDisabledOrderTypesForStockRevert(): array;
}
