<?php
/**
 * Context
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface as Timezone;
use Magento\Framework\Stdlib\ArrayManager as ArrayManager;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\DB\Adapter\AdapterInterface as Connection;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Netsteps\MarketplaceSales\Api\Data\MetadataInterfaceFactory as MetadataFactory;

/**
 * Class Context
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier
 */
class Context
{
    /**
     * @var Timezone
     */
    private Timezone $_timezone;

    /**
     * @var ArrayManager
     */
    private ArrayManager $_arrayManager;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @var Connection
     */
    private Connection $_connection;

    /**
     * @var MetadataFactory
     */
    private MetadataFactory $_metadataFactory;

    /**
     * @param Timezone $timezone
     * @param ArrayManager $arrayManager
     * @param EventManager $eventManager
     * @param ResourceConnection $resourceConnection
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(
        Timezone $timezone,
        ArrayManager $arrayManager,
        EventManager $eventManager,
        ResourceConnection $resourceConnection,
        MetadataFactory $metadataFactory
    )
    {
        $this->_timezone = $timezone;
        $this->_arrayManager = $arrayManager;
        $this->_eventManager = $eventManager;
        $this->_metadataFactory = $metadataFactory;
        $this->_connection = $resourceConnection->getConnection();
    }

    /**
     * @return Timezone
     */
    public function getTimezone(): Timezone
    {
        return $this->_timezone;
    }

    /**
     * @return EventManager
     */
    public function getEventManager(): EventManager
    {
        return $this->_eventManager;
    }

    /**
     * @return ArrayManager
     */
    public function getArrayManager(): ArrayManager
    {
        return $this->_arrayManager;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->_connection;
    }

    /**
     * @return MetadataFactory
     */
    public function getMetadataFactory(): MetadataFactory
    {
        return $this->_metadataFactory;
    }
}
