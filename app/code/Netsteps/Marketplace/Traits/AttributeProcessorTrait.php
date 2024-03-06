<?php
/**
 * AttributeProcessorTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Traits;

use Netsteps\Marketplace\Model\Feed\ItemInterface;

/**
 * Trait AttributeProcessorTrait
 * @package Netsteps\Marketplace\Traits
 */
trait AttributeProcessorTrait
{

    /**
     * Process data to change labels with option ids for attributes
     * that are type of: select, multiselect and swatch.
     *
     * @param array $data
     * @param array $optionAttributes
     * @return void
     */
    protected function processAttributes(array &$data, array $optionAttributes): void {
        foreach ($optionAttributes as $attributeCode) {
            if (isset($data[$attributeCode])){
                $optionText = $data[$attributeCode];
                $optionValue = $this->getAttributeOptionIdByOptionText($attributeCode, $optionText);

                if (!is_null($optionValue)){
                    $data[$attributeCode] = $optionValue;
                }
            }
        }
    }

    /**
     * Get attribute option if attribute exists and is of type select, multiselect or any type of swatch
     * @param string $attributeCode
     * @param string $optionText
     * @return int|null
     */
    private function getAttributeOptionIdByOptionText(string $attributeCode, string $optionText): ?int {
        $optionText = mb_strtoupper(mb_strtolower($optionText));
        $attributeManagement = $this->getAttributeManagement();
        return $attributeManagement->getOptionId($attributeCode, $optionText) ??
            $attributeManagement->createOption($attributeCode, $optionText);
    }

    /**
     * Get the attribute management model
     * @return \Netsteps\Marketplace\Model\Product\AttributeManagementInterface
     */
    abstract protected function getAttributeManagement(): \Netsteps\Marketplace\Model\Product\AttributeManagementInterface;

    /**
     * Process item categories
     * @param array $data
     * @return void
     */
    protected function processItemCategories(array &$data): void {
        if (isset($data[ItemInterface::CATEGORIES])) {
            $newCategories = array_map([$this, 'castToInt'], explode(',', $data[ItemInterface::CATEGORIES]));
            sort($newCategories);
            $data['category_ids'] = $newCategories;
            unset($data[ItemInterface::CATEGORIES]);
        }
    }

    /**
     * Cast to int
     * @param mixed $value
     * @return int
     */
    protected function castToInt(mixed $value): int {
        return (int)$value;
    }
}
