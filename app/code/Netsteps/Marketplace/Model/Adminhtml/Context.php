<?php
/**
 * Context
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */
namespace Netsteps\Marketplace\Model\Adminhtml;

use Netsteps\Logger\Model\Logger;
use Magento\Framework\Registry as Registry;
use Magento\Framework\Stdlib\ArrayManager as ArrayManager;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface as Timezone;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Netsteps\Marketplace\Api\FeedRepositoryInterface as FeedRepository;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface as FeedMetadata;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Netsteps\Seller\Model\Admin\SellerManagementInterface as AdminSellerManagement;


/**
 * Class Context
 * @package Netsteps\Marketplace\Controller\Adminhtml
 */
class Context
{
    /**
     * @var Logger
     */
    private Logger $_logger;

    /**
     * @var Registry
     */
    private Registry $_registry;

    /**
     * @var ArrayManager
     */
    private ArrayManager $_arrayManager;

    /**
     * @var StoreManager
     */
    private StoreManager $_storeManager;

    /**
     * @var Timezone
     */
    private Timezone $_timezone;

    /**
     * @var AdminSession
     */
    private AdminSession $_adminSession;

    /**
     * @var FeedRepository
     */
    private FeedRepository $_feedRepository;

    /**
     * @var FeedMetadata
     */
    private FeedMetadata $_feedMetadata;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @var AdminSellerManagement
     */
    private AdminSellerManagement $_sellerManager;

    /**
     * @param Registry $registry
     * @param Logger $logger
     * @param ArrayManager $arrayManager
     * @param StoreManager $storeManager
     * @param Timezone $timezone
     * @param AdminSession $adminSession
     * @param FeedRepository $feedRepository
     * @param FeedMetadata $feedMetadata
     * @param EventManager $eventManager
     * @param AdminSellerManagement $sellerManagement
     */
    public function __construct(
        Registry $registry,
        Logger $logger,
        ArrayManager $arrayManager,
        StoreManager $storeManager,
        Timezone $timezone,
        AdminSession $adminSession,
        FeedRepository $feedRepository,
        FeedMetadata $feedMetadata,
        EventManager $eventManager,
        AdminSellerManagement $sellerManagement
    )
    {
        $this->_registry = $registry;
        $this->_logger = $logger;
        $this->_arrayManager = $arrayManager;
        $this->_storeManager = $storeManager;
        $this->_timezone = $timezone;
        $this->_adminSession = $adminSession;
        $this->_feedRepository = $feedRepository;
        $this->_feedMetadata = $feedMetadata;
        $this->_eventManager = $eventManager;
        $this->_sellerManager = $sellerManagement;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->_logger;
    }

    /**
     * @return Registry
     */
    public function getRegistry(): Registry
    {
        return $this->_registry;
    }

    /**
     * @return ArrayManager
     */
    public function getArrayManager(): ArrayManager
    {
        return $this->_arrayManager;
    }

    /**
     * @return Timezone
     */
    public function getTimezone(): Timezone
    {
        return $this->_timezone;
    }

    /**
     * @return StoreManager
     */
    public function getStoreManager(): StoreManager
    {
        return $this->_storeManager;
    }

    /**
     * @return AdminSession
     */
    public function getAdminSession(): AdminSession
    {
        return $this->_adminSession;
    }

    /**
     * @return FeedRepository
     */
    public function getFeedRepository(): FeedRepository
    {
        return $this->_feedRepository;
    }

    /**
     * @return FeedMetadata
     */
    public function getFeedMetadata(): FeedMetadata
    {
        return $this->_feedMetadata;
    }

    /**
     * @return EventManager
     */
    public function getEventManager(): EventManager
    {
        return $this->_eventManager;
    }

    /**
     * @return AdminSellerManagement
     */
    public function getSellerManager(): AdminSellerManagement
    {
        return $this->_sellerManager;
    }
}
