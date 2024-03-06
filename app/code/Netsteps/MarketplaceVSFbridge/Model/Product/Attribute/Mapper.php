<?php
/**
 * Mapper
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Model\Product\Attribute;

use Magento\Eav\Model\Entity\Attribute;


/**
 * Class Mapper
 * @package Netsteps\MarketplaceVSFbridge\Model\Product\Attribute
 */
class Mapper implements MapperInterface
{
    /**
     * @var array
     */
    private array $_mappings;

    /**
     * @param array $mappings
     */
    public function __construct(array $mappings = [])
    {
        $this->_mappings = $mappings;
    }

    /**
     * @inheritDoc
     */
    public function getMap(Attribute $attribute): ?array
    {
        $attributeCode = $attribute->getAttributeCode();
        if (!array_key_exists($attributeCode, $this->_mappings)){
            return null;
        }
        $attributeMapping = $this->_mappings[$attributeCode];

        if (!is_array($attributeMapping)){
            return ['type' => $attributeMapping];
        }

        $mapping = $this->prepareAttributeMapping($attributeMapping);
        return ['properties' => $mapping];
    }

    /**
     * Prepare attribute mapping recursive
     * @param array $map
     * @return array
     */
    private function prepareAttributeMapping(array $map): array {
        $mapping = [];

        foreach ($map as $key => $value) {
            if (is_array($value)) {
                $mapping[$key] = ['properties' => $this->prepareAttributeMapping($value)];
            } else {
                $mapping[$key] = ['type' => $value];
            }
        }

        return $mapping;
    }

    /**
     * @inheritDoc
     */
    public function mapValues(array $data): array
    {
        $mappingsToDecode = array_filter($this->_mappings, function ($item) {
            return is_array($item);
        });

        $attributeCodesToDecode = array_keys($mappingsToDecode);

        if (empty($attributeCodesToDecode)){
            return $data;
        }

        $decoded = new JsonDecoder($attributeCodesToDecode, $this->_mappings);
        return array_map([$decoded, 'decode'], $data);
    }
}

