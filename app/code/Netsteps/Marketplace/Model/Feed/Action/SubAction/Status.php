<?php
/**
 * Status
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action\SubAction;

use Divante\VsbridgeIndexerCore\Indexer\Action\AbstractAction;
use Divante\VsbridgeIndexerCore\Indexer\Action\ActionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface as ProductAttributeRepository;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface as Connection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Netsteps\Marketplace\Model\Feed\Action\SubActionInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Product\Attribute\ChildNotVisibleInFrontInterface;
use Netsteps\Seller\Api\Data\SellerInterface;
use Divante\VsbridgeIndexerCatalog\Model\Indexer\Product;

/**
 * Class Status
 * @package Netsteps\Marketplace\Model\Feed\Action\SubAction
 */
class Status implements SubActionInterface
{
    const ACTION_CODE = 'status';

    /**
     * @var ProductCollectionFactory
     */
    private ProductCollectionFactory $_productCollectionFactory;

    /**
     * @var ProductAttributeRepository
     */
    private ProductAttributeRepository $_attributeRepository;

    /**
     * @var \Netsteps\Marketplace\Model\Product\Attribute\ChildNotVisibleInFrontInterface
     */
    private ChildNotVisibleInFrontInterface $_childNotVisibleInFront;

    /**
     * @var Connection
     */
    private Connection $_connection;

    /**
     * @var \Divante\VsbridgeIndexerCore\Indexer\Action\AbstractAction
     */
    private AbstractAction $_indexer;

    /**
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductAttributeRepository $productAttributeRepository
     * @param \Netsteps\Marketplace\Model\Product\Attribute\ChildNotVisibleInFrontInterface $childNotVisibleInFront
     * @param ResourceConnection $resourceConnection
     * @param \Divante\VsbridgeIndexerCore\Indexer\Action\ActionFactory $indexerFactory
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        ProductAttributeRepository $productAttributeRepository,
        ChildNotVisibleInFrontInterface $childNotVisibleInFront,
        ResourceConnection $resourceConnection,
        ActionFactory $indexerFactory
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_attributeRepository = $productAttributeRepository;
        $this->_childNotVisibleInFront = $childNotVisibleInFront;
        $this->_connection = $resourceConnection->getConnection();
        $this->_indexer = $indexerFactory->create('rows', 'product');
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process(array $data, SellerInterface $seller): void
    {
        if (empty($data) || !$seller->getEntityId()){
            return;
        }

        $sku = array_map([$this, 'exportSku'], $data);
        $idsToDisable = $this->getNonExistedActiveProductIds($sku, $seller->getEntityId());

        if (empty($idsToDisable['ids'])){
            return;
        }

        $this->disabledProducts($idsToDisable['ids']);

        if(!empty($idsToDisable['reset_data_products'])) {
            $this->_childNotVisibleInFront->setChildAsNotVisibleInFront($idsToDisable['reset_data_products'], $seller->getSourceCode());
        }

        $this->_indexer->execute($idsToDisable['ids']);
    }

    /**
     * Disable product ids given
     * @param int[] $ids
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function disabledProducts(array $ids): void {
        /** @var  $statusAttribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $statusAttribute = $this->_attributeRepository->get('is_visible_in_front');
        $attributeId = $statusAttribute->getAttributeId();
        $table = $statusAttribute->getBackendTable();

        $inserts = [];

        foreach ($ids as $id) {
            $inserts[] = [
                'attribute_id' => $attributeId,
                'entity_id' => $id,
                'store_id' => 0,
                'value' => 0
            ];
        }

        $this->_connection->insertOnDuplicate($table, $inserts, ['value']);
    }

    /**
     * Get item's sku
     * @param ItemInterface $item
     * @return string
     */
    private function exportSku(ItemInterface $item): string {
        return $item->getSku();
    }

    /**
     * Get product ids from distributor that are enabled and their skus do not exist in
     * distributor's feed.
     *
     * @param array $sku
     * @param int $sellerId
     * @return array
     */
    private function getNonExistedActiveProductIds(array $sku, int $sellerId): array {
        /** @var ProductCollection $products */
        $products = $this->_productCollectionFactory->create();
        $products->addAttributeToSelect(['visibility', 'ns_distributor', 'is_visible_in_front']);
        $products->addAttributeToFilter('is_visible_in_front', ['neq' => 0])
            ->addFieldToFilter('sku', ['nin' => $sku])
            ->addAttributeToFilter('ns_distributor', $sellerId);

        $products = $products->getItems();

        $result = [
            'ids' => [],
            'reset_data_products' => []
        ];

        foreach ($products as $product){
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $result['ids'][] = $product->getId();

            if($product->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE){
                $result['reset_data_products'][] = $product;
            }
        }

        return $result;
    }
}
