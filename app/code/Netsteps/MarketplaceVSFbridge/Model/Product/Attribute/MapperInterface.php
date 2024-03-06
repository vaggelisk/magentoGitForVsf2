<?php
/**
 * MapperInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Model\Product\Attribute;

use Magento\Eav\Model\Entity\Attribute;

/**
 * Interface MapperInterface
 * @package Netsteps\MarketplaceVSFbridge\Model\Product\Attribute
 */
interface MapperInterface
{
    /**
     * Get custom mapping for given attribute
     * @param Attribute $attribute
     * @return array|null
     */
    public function getMap(Attribute $attribute): ?array;

    /**
     * Map array values
     * @param array $data
     * @return array
     */
    public function mapValues(array $data): array;
}
