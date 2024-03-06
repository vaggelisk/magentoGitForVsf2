<?php
/**
 * GeneralConfiguration
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\System\Config;

use Netsteps\Marketplace\Model\System\Config\AbstractConfiguration;

/**
 * Class GeneralConfiguration
 * @package Netsteps\Marketplace\Model\System\Config
 */
class GeneralConfiguration extends AbstractConfiguration implements GeneralConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function isSourceSelectionDisabled(): bool
    {
        return (bool)(int)$this->getConfig(self::FIELD_DISABLE_SOURCE_SELECTION);
    }

    /**
     * @inheritDoc
     */
    public function getDisabledOrderTypesForStockDeduction(): array
    {
        $value = $this->getConfig(self::FIELD_DISABLE_ORDER_TYPES_DEDUCTION);
        return $value ? explode(',', (string)$value) : [];
    }

    /**
     * @inheritDoc
     */
    public function getDisabledOrderTypesForStockRevert(): array
    {
        $value = $this->getConfig(self::FIELD_DISABLE_ORDER_TYPES_REVERT);
        return $value ? explode(',', (string)$value) : [];
    }
}
