<?php
/**
 * Context
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Item\Processor;

use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface as AttributeRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\ConfigurableProduct\Api\OptionRepositoryInterface as ConfigurableOptionRepository;
use Magento\ConfigurableProduct\Api\Data\OptionInterfaceFactory as ConfigurableOptionFactory;
use Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory as ConfigurableOptionValueFactory;
use Magento\ConfigurableProduct\Api\LinkManagementInterface as LinkManagement;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Netsteps\Marketplace\Api\ProductHistoryRepositoryInterface as ProductHistoryRepository;
use Netsteps\Marketplace\Model\Product\ImageProcessor as ProductImageProcessor;
use Netsteps\Marketplace\Api\StockManagementInterface as StockManagement;

/**
 * Class Context
 * @package Netsteps\Marketplace\Model\Product\Item\Processor
 */
class Context
{
    /**
     * @var ProductFactory
     */
    private ProductFactory $_productFactory;

    /**
     * @var ProductRepository
     */
    private ProductRepository $_productRepository;

    /**
     * @var AttributeRepository
     */
    private AttributeRepository $_attributeRepository;

    /**
     * @var ProductCollectionFactory
     */
    private ProductCollectionFactory $_productCollectionFactory;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @var StoreManager
     */
    private StoreManager $_storeManager;

    /**
     * @var ProductHistoryRepository
     */
    private ProductHistoryRepository $_productHistoryRepository;

    /**
     * @var ProductImageProcessor
     */
    private ProductImageProcessor $_productImageProcessor;

    /**
     * @var ConfigurableOptionRepository
     */
    private ConfigurableOptionRepository $_configurableOptionRepository;

    /**
     * @var ConfigurableOptionFactory
     */
    private ConfigurableOptionFactory $_configurableOptionFactory;

    /**
     * @var ConfigurableOptionValueFactory
     */
    private ConfigurableOptionValueFactory $_configurableOptionValueFactory;

    /**
     * @var StockManagement
     */
    private StockManagement $_stockManagement;

    /**
     * @var LinkManagement
     */
    private LinkManagement $_linkManagement;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @param ProductHistoryRepository $productHistoryRepository
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param AttributeRepository $attributeRepository
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ResourceConnection $resourceConnection
     * @param StoreManager $storeManager
     * @param ProductImageProcessor $imageProcessor
     * @param ConfigurableOptionRepository $configurableOptionRepository
     * @param ConfigurableOptionFactory $configurableOptionFactory
     * @param ConfigurableOptionValueFactory $configurableOptionValueFactory
     * @param StockManagement $stockManagement
     * @param LinkManagement $linkManagement
     * @param EventManager $eventManager
     */
    public function __construct(
        ProductHistoryRepository $productHistoryRepository,
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        AttributeRepository $attributeRepository,
        ProductCollectionFactory $productCollectionFactory,
        ResourceConnection $resourceConnection,
        StoreManager $storeManager,
        ProductImageProcessor $imageProcessor,
        ConfigurableOptionRepository $configurableOptionRepository,
        ConfigurableOptionFactory $configurableOptionFactory,
        ConfigurableOptionValueFactory $configurableOptionValueFactory,
        StockManagement $stockManagement,
        LinkManagement $linkManagement,
        EventManager $eventManager
    )
    {
        $this->_productHistoryRepository = $productHistoryRepository;
        $this->_productFactory = $productFactory;
        $this->_productRepository = $productRepository;
        $this->_attributeRepository = $attributeRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_connection = $resourceConnection->getConnection();
        $this->_storeManager = $storeManager;
        $this->_productImageProcessor = $imageProcessor;
        $this->_configurableOptionRepository = $configurableOptionRepository;
        $this->_configurableOptionFactory = $configurableOptionFactory;
        $this->_configurableOptionValueFactory = $configurableOptionValueFactory;
        $this->_stockManagement = $stockManagement;
        $this->_linkManagement = $linkManagement;
        $this->_eventManager = $eventManager;
    }

    /**
     * @return EventManager
     */
    public function getEventManager(): EventManager
    {
        return $this->_eventManager;
    }

    /**
     * @return StoreManager
     */
    public function getStoreManager(): StoreManager
    {
        return $this->_storeManager;
    }

    /**
     * @return ProductRepository
     */
    public function getProductRepository(): ProductRepository
    {
        return $this->_productRepository;
    }

    /**
     * @return AdapterInterface
     */
    public function getConnection(): AdapterInterface
    {
        return $this->_connection;
    }

    /**
     * @return AttributeRepository
     */
    public function getAttributeRepository(): AttributeRepository
    {
        return $this->_attributeRepository;
    }

    /**
     * @return ProductCollectionFactory
     */
    public function getProductCollectionFactory(): ProductCollectionFactory
    {
        return $this->_productCollectionFactory;
    }

    /**
     * @return ProductFactory
     */
    public function getProductFactory(): ProductFactory
    {
        return $this->_productFactory;
    }

    /**
     * @return ProductHistoryRepository
     */
    public function getProductHistoryRepository(): ProductHistoryRepository
    {
        return $this->_productHistoryRepository;
    }

    /**
     * @return ProductImageProcessor
     */
    public function getProductImageProcessor(): ProductImageProcessor
    {
        return $this->_productImageProcessor;
    }

    /**
     * @return ConfigurableOptionFactory
     */
    public function getConfigurableOptionFactory(): ConfigurableOptionFactory
    {
        return $this->_configurableOptionFactory;
    }

    /**
     * @return ConfigurableOptionRepository
     */
    public function getConfigurableOptionRepository(): ConfigurableOptionRepository
    {
        return $this->_configurableOptionRepository;
    }

    /**
     * @return ConfigurableOptionValueFactory
     */
    public function getConfigurableOptionValueFactory(): ConfigurableOptionValueFactory
    {
        return $this->_configurableOptionValueFactory;
    }

    /**
     * @return StockManagement
     */
    public function getStockManagement(): StockManagement
    {
        return $this->_stockManagement;
    }

    /**
     * @return LinkManagement
     */
    public function getLinkManagement(): LinkManagement
    {
        return $this->_linkManagement;
    }
}
