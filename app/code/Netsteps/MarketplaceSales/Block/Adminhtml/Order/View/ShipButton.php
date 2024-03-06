<?php
/**
 * ShipButton
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Block\Adminhtml\Order\View;

use Magento\Framework\App\ObjectManager;
use Netsteps\MarketplaceSales\Model\System\Config\GeneralConfigurationInterface as Config;
use Netsteps\MarketplaceSales\Traits\ConfigurationTrait;

/**
 * Class ShipButton
 * @package Netsteps\MarketplaceSales\Block\Adminhtml\Order\View
 */
class ShipButton extends \Magento\InventoryShippingAdminUi\Block\Adminhtml\Order\View\ShipButton
{
    use ConfigurationTrait;

    private ?Config $_config = null;

    /**
     * Override ship button update process
     * @return $this
     */
    protected function _prepareLayout(): self
    {
        if ($this->getConfig()->isSourceSelectionDisabled()){
            return $this;
        }
        return parent::_prepareLayout();
    }
}
