<?php
/**
 * TransformDataPlugin
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Plugin\DataProvider;

use Netsteps\MarketplaceVSFbridge\Model\Product\Attribute\MapperInterface as Mapper;

/**
 * Class TransformDataPlugin
 * @package Netsteps\MarketplaceVSFbridge\Plugin\DataProvider
 */
class TransformDataPlugin
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
     * Transform mapped data to array
     * @param \Divante\VsbridgeIndexerCatalog\Model\ResourceModel\Product\AttributeDataProvider $dataProvider
     * @param array $result
     * @param int $storeId
     * @param array $entityIds
     * @param array|null $requiredAttributes
     * @return array
     */
    public function afterLoadAttributesData(
        \Divante\VsbridgeIndexerCatalog\Model\ResourceModel\Product\AttributeDataProvider $dataProvider,
        array $result,
        int $storeId,
        array $entityIds,
        ?array $requiredAttributes = null
    ): array
    {
        return $this->_mapper->mapValues($result);
    }
}
