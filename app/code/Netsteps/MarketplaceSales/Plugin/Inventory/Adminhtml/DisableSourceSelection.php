<?php
/**
 * DisableSourceSelection
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Inventory\Adminhtml;

use Magento\Framework\Event\Observer as EventObserver;
use Netsteps\MarketplaceSales\Model\System\Config\GeneralConfigurationInterface as Config;

/**
 * Class DisableSourceSelection
 * @package Netsteps\MarketplaceSales\Plugin\Inventory\Adminhtml
 */
class DisableSourceSelection
{
    /**
     * @var Config
     */
    private Config $_config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Disable source selection screen on admin area when try to ship an order.
     * @param \Magento\InventoryShippingAdminUi\Observer\NewShipmentLoadBefore $newShipmentObserver
     * @param callable $proceed
     * @param EventObserver $observer
     * @return void
     */
    public function aroundExecute(
        \Magento\InventoryShippingAdminUi\Observer\NewShipmentLoadBefore $newShipmentObserver,
        callable $proceed,
        EventObserver $observer
    ): void {
        if ($this->_config->isSourceSelectionDisabled()){
            return;
        }

        $proceed($observer);
    }
}
