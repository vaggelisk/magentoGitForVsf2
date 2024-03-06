<?php
/**
 * AddSellerAttributeMapping
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Plugin;

use Divante\VsbridgeIndexerCore\Api\Mapping\FieldInterface;
use Magento\Eav\Model\Entity\Attribute;
use Netsteps\MarketplaceVSFbridge\Model\Product\Attribute\MapperInterface as Mapper;
/**
 * Class AddSellerAttributeMapping
 * @package Netsteps\MarketplaceVSFbridge\Plugin
 */
class AddSellerAttributeMapping
{
    /**
     * @var Mapper
     */
    private Mapper $_mapper;

    /**
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->_mapper = $mapper;
    }

    /**
     * Add custom attribute mapping
     * @param \Divante\VsbridgeIndexerCatalog\Index\Mapping\Product $productMapping
     * @param callable $proceed
     * @param Attribute $attribute
     * @return array
     */
    public function aroundGetAttributeMapping(
        \Divante\VsbridgeIndexerCatalog\Index\Mapping\Product $productMapping,
        callable $proceed,
        Attribute $attribute
    ): array {

        if ($mapping = $this->_mapper->getMap($attribute)) {
            return [$attribute->getAttributeCode() => $mapping];
        }

        return $proceed($attribute);
    }

    /**
     * Set min_price and max_price types for elastic indexer
     * @param \Divante\VsbridgeIndexerCatalog\Index\Mapping\Product $productMappingProperties
     * @param array $result
     * @return array
     */
    public function afterGetMappingProperties(
        \Divante\VsbridgeIndexerCatalog\Index\Mapping\Product $productMappingProperties,
        array $result
    ): array {
        $result['properties']['min_price']['type'] = FieldInterface::TYPE_DOUBLE;
        $result['properties']['max_price']['type'] = FieldInterface::TYPE_DOUBLE;
        return $result;
    }
}
