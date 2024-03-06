<?php
/**
 * AbstractModifier
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Netsteps\MarketplaceSales\Api\Data\MetadataInterface;
use Netsteps\MarketplaceSales\Api\Data\MetadataInterfaceFactory;
use Netsteps\MarketplaceSales\Model\Marketplace\Order\ModifierInterface;

/**
 * Class AbstractModifier
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier
 */
abstract class AbstractModifier implements ModifierInterface
{
    /**
     * @var TimezoneInterface
     */
    protected TimezoneInterface $_timezone;

    /**
     * @var ArrayManager
     */
    protected ArrayManager $_arrayManager;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $_eventManager;

    /**
     * @var AdapterInterface
     */
    protected AdapterInterface  $_connection;

    /**
     * @var MetadataInterfaceFactory
     */
    private MetadataInterfaceFactory $_metadataFactory;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->_timezone = $context->getTimezone();
        $this->_arrayManager = $context->getArrayManager();
        $this->_eventManager = $context->getEventManager();
        $this->_connection = $context->getConnection();
        $this->_metadataFactory = $context->getMetadataFactory();
    }

    /**
     * Create a new metadata object
     * @return MetadataInterface
     */
    protected function createMetadataOption(): MetadataInterface {
        return $this->_metadataFactory->create();
    }
}
