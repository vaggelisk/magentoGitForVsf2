<?php
/**
 * Context
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Data;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface as ProductAttributeRepository;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface as Connection;
use Magento\Framework\Api\SearchCriteriaBuilder as SearchCriteriaBuilder;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Psr\Log\LoggerInterface;

/**
 * Class Context
 * @package Netsteps\Marketplace\Model\Product\Data
 */
class Context
{
    /**
     * @var ProductRepository
     */
    private ProductRepository $_productRepository;

    /**
     * @var ProductAttributeRepository
     */
    private ProductAttributeRepository $_productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @var Connection
     */
    private Connection $_connection;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param ProductRepository $productRepository
     * @param ProductAttributeRepository $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ResourceConnection $resourceConnection
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductAttributeRepository $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ResourceConnection $resourceConnection,
        LoggerPool $loggerPool
    )
    {
        $this->_productRepository = $productRepository;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_connection = $resourceConnection->getConnection();
        $this->_logger = $loggerPool->getLogger('base');
    }

    /**
     * @return ProductRepository
     */
    public function getProductRepository(): ProductRepository
    {
        return $this->_productRepository;
    }

    /**
     * @return ProductAttributeRepository
     */
    public function getProductAttributeRepository(): ProductAttributeRepository
    {
        return $this->_productAttributeRepository;
    }

    /**
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return $this->_searchCriteriaBuilder;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->_connection;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->_logger;
    }
}
