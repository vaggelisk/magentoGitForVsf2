<?php
/**
 * ItemProcessorPool
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

/**
 * Class ItemProcessorPool
 * @package Netsteps\Marketplace\Model\Product
 */
class ItemProcessorPool implements ItemProcessorPoolInterface
{
    /**
     * @var ItemProcessorInterface[]
     */
    private array $processors;

    /**
     * @param ItemProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * @inheritDoc
     */
    public function getProcessorByProductType(string $productType): ?ItemProcessorInterface
    {
        if(array_key_exists($productType, $this->processors)){
            return $this->processors[$productType];
        }
        return null;
    }
}
