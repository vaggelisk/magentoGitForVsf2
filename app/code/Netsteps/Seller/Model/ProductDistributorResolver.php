<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\App\ResourceConnection;
use Netsteps\Seller\Api\ProductDistributorResolverInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;
use Netsteps\Seller\Model\Product\AttributeResolver;

class ProductDistributorResolver extends AbstractConnection implements ProductDistributorResolverInterface
{
    private SellerRepositoryInterface $sellerRepository;

    private AttributeResolver $attributeResolver;

    private int $attributeId;

    /**
     * @param ResourceConnection $_connection
     * @param SellerRepositoryInterface $sellerRepository
     * @param AttributeResolver $attributeResolver
     */
    public function __construct(
        ResourceConnection        $_connection,
        SellerRepositoryInterface $sellerRepository,
        AttributeResolver         $attributeResolver,
    )
    {
        $this->sellerRepository = $sellerRepository;
        $this->attributeResolver = $attributeResolver;
        $this->attributeId = $this->attributeResolver->getDistributorAttributeId();

        parent::__construct($_connection);
    }

    /**
     * @inheritDoc
     */
    public function getBySku(string $sku): ?\Netsteps\Seller\Api\Data\SellerInterface
    {
        if ($this->canProceed()) {
            $productId = $this->fetchProductIdBySku($sku);
            if ($productId) {
                $sellerId = $this->getDistributorIdByProductId($productId);
                if ($sellerId) {
                    return $this->sellerRepository->getById($sellerId);
                }
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getByProductId(int $id): ?\Netsteps\Seller\Api\Data\SellerInterface
    {
        if($this->canProceed()){
            $sellerId = $this->getDistributorIdByProductId($id);
            if ($sellerId) {
                return $this->sellerRepository->getById($sellerId);
            }
        }
        return null;
    }

    /**
     * @param int $id
     * @return string
     */
    private function getDistributorIdByProductId(int $id): ?int
    {
        $select = $this->_connection
            ->select()
            ->from(['eav' => 'catalog_product_entity_int'], ['eav.value'])
            ->where('eav.entity_id = ? ', $id)
            ->where('eav.attribute_id = ? ', $this->attributeId);
        return (int)$this->_connection->fetchOne($select) ?? null;
    }

    /**
     * @param string $sku
     * @return string
     */
    private function fetchProductIdBySku(string $sku): ?int
    {
        $select = $this->_connection
            ->select()
            ->from(
                ['e' => 'catalog_product_entity'],
                ['e.entity_id']
            )
            ->where('e.sku = ? ', $sku);

        return (int)$this->_connection->fetchOne($select) ?? null;
    }

    /**
     * @return bool
     */
    private function canProceed(): bool
    {
        return !is_null($this->attributeId) && $this->attributeId > 0;
    }

}
