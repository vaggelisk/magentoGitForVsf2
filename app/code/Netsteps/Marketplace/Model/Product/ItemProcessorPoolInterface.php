<?php
/**
 * ItemProcessorPoolInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

/**
 * Interface ItemProcessorPoolInterface
 * @package Netsteps\Marketplace\Model\Product
 */
interface ItemProcessorPoolInterface
{
    /**
     * Get item processor by product type
     * @param string $productType
     * @return ItemProcessorInterface|null
     */
    public function getProcessorByProductType(string $productType): ?ItemProcessorInterface;
}
