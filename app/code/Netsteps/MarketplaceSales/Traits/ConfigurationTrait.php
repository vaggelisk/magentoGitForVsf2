<?php
/**
 * ConfigurationTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Traits;

use Magento\Framework\App\ObjectManager;
use Netsteps\MarketplaceSales\Model\System\Config\GeneralConfigurationInterface as Config;

/**
 * Trait ConfigurationTrait
 * @package Netsteps\MarketplaceSales\Traits
 */
trait ConfigurationTrait
{
    /**
     * @var Config|null
     */
    private ?Config $_config = null;


    /**
     * Get or create config object
     * @return Config
     */
    protected function getConfig(): Config {
        if (!$this->_config){
            $this->_config = ObjectManager::getInstance()->get(Config::class);
        }
        return $this->_config;
    }
}
