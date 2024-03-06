<?php
/**
 * Repository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Data;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface as ProductAttributeRepository;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder as SearchCriteriaBuilder;
use Magento\Framework\DB\Adapter\AdapterInterface as Connection;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Repository
 * @package Netsteps\Marketplace\Model\Product\Data
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ProductRepository
     */
    protected ProductRepository $_productRepository;

    /**
     * @var ProductAttributeRepository
     */
    protected ProductAttributeRepository $_productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @var Connection
     */
    protected Connection $_connection;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $_logger;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->_productRepository = $context->getProductRepository();
        $this->_productAttributeRepository = $context->getProductAttributeRepository();
        $this->_searchCriteriaBuilder = $context->getSearchCriteriaBuilder();
        $this->_connection = $context->getConnection();
        $this->_logger = $context->getLogger();
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getRetailPrices(array $productIds, int $storeId = 0): array
    {
        if (empty($productIds)){
            return [];
        }

        try {
            /** @var  $retailPriceAttribute  \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            $retailPriceAttribute = $this->_productAttributeRepository->get(ItemInterface::RETAIL_PRICE);
            $attributeId = $retailPriceAttribute->getAttributeId();

            $select = $this->_connection->select()
                ->from(
                    ['e' => $this->getTableName('catalog_product_entity')],
                    ['entity_id']
                )->join(
                    ['rp' => $this->getTableName($retailPriceAttribute->getBackendTable())],
                    "e.entity_id = rp.entity_id AND rp.attribute_id = {$attributeId} AND rp.store_id = {$storeId}",
                    ['retail_price' => 'value']
                )
                ->where('e.entity_id IN (?)', $productIds)
                ->where(
                    'e.type_id IN (?)', \Netsteps\Marketplace\Model\Data\MerchantItemData::getAvailableProductTypesToIndex()
                );

            $data = $this->_connection->fetchAssoc($select);

            $keys = array_column($data, 'entity_id');
            $values = array_column($data, 'retail_price');

            return array_combine($keys, $values);
        } catch (\Exception $e){
            $this->_logger->error(
                __(
                    'Exception on method %1 at class %2. Reason: %3',
                    [__FUNCTION__, get_class($this), $e->getMessage()]
                )
            );

            throw $e;
        }
    }

    /**
     * Get original table name with any prefix if exists
     * @param string $tableName
     * @return string
     */
    private function getTableName(string $tableName): string {
        return $this->_connection->getTableName($tableName);
    }
}
