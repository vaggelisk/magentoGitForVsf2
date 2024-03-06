<?php
/**
 * ItemVariationTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Traits;

use Magento\Framework\Validation\ValidationException;
use Netsteps\Marketplace\Model\Feed\Item\Variation;
use Netsteps\Marketplace\Model\Feed\Item\VariationInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;

/**
 * Trait ItemVariationTrait
 * @package Netsteps\Marketplace\Traits
 */
trait ItemVariationTrait
{
    /**
     * Create a new variation
     * @return VariationInterface
     */
    protected function createVariation(): VariationInterface {
        return new Variation();
    }

    /**
     * @throws ValidationException
     */
    protected function validateVariations(ItemInterface $item, array $rawData): void {
        $this->applyVariations($item, $rawData);

        $variations = $item->getVariations();

        if (empty($variations)){
            return;
        }

        $previousVariationAttributes = null;

        foreach ($variations as $variation){
            $attributes = array_keys($variation->getAttributes());
            sort($attributes);

            if (!$previousVariationAttributes){
                $previousVariationAttributes = $attributes;
            } else {
                if ($previousVariationAttributes === $attributes){
                    continue;
                }

                throw new ValidationException(
                    __('Invalid variations for sku: %1', $item->getSku())
                );
            }
        }

        $item->setVariationAttributeCodes($previousVariationAttributes);
    }

    /**
     * Normalize variation data of item
     * @param ItemInterface $item
     * @param array $rawData
     * @return void
     */
    abstract protected function applyVariations(ItemInterface $item, array $rawData): void;
}
