<?php
/**
 * AttributeManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

/**
 * Interface AttributeManagementInterface
 * @package Netsteps\Marketplace\Model\Product
 */
interface AttributeManagementInterface
{

    /**
     * Get option id for given attribute based on label
     * @param string $attributeCode
     * @param string $label
     * @return int|null
     */
    public function getOptionId(string $attributeCode, string $label): ?int;

    /**
     * Create a new option for given attribute code
     * @param string $attributeCode
     * @param string $label
     * @param int|null $sortOrder
     * @return int|null
     */
    public function createOption(string $attributeCode, string $label, ?int $sortOrder = null): ?int;

    /**
     * Get product attribute by code
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     */
    public function getProductAttribute(string $attributeCode): \Magento\Catalog\Api\Data\ProductAttributeInterface;
}
