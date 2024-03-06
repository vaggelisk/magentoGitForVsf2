<?php
/**
 * UpgradeData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\Price;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Product\Attribute\Source\EstimatedDelivery;

/**
 * Class UpgradeData
 * @package Netsteps\Marketplace\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $_eavSetupFactory;

    /**
     * @var EavSetup|null
     */
    private ?EavSetup $_eavSetup = null;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->addBaseAttributes();
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->addWysiwygInfoAttributes();
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $this->createEstimatedDeliveryAttribute();
        }

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $this->createEanAttribute();
        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $this->createSeasonAttribute();
        }

        $setup->endSetup();
    }

    /**
     * Add base attributes needed for products
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addBaseAttributes(): void
    {
        //Add Retail price attribute
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ItemInterface::RETAIL_PRICE,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'decimal',
                'input' => 'price',
                'label' => 'Retail Price',
                'backend' => Price::class,
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to' => 'simple,virtual',
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true
            ]
        );

        //Add MPN attribute
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ItemInterface::MPN,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'MPN',
                'required' => true,
                'sort_order' => 20,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true
            ]
        );
    }

    /**
     * Add wysiwyg attributes needed for products to set up html information
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addWysiwygInfoAttributes(): void
    {
        //Add Size and Fit information attribute
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ItemInterface::SIZE_INFO,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Size and Fit Information',
                'required' => false,
                'sort_order' => 30,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'used_for_sort_by' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_wysiwyg_enabled' => true
            ]
        );

        //Add composition information attribute
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ItemInterface::COMPOSITION_INFO,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Composition Information',
                'required' => false,
                'sort_order' => 40,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'used_for_sort_by' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_wysiwyg_enabled' => true
            ]
        );
    }

    /**
     * Create estimated delivery attribute
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function createEstimatedDeliveryAttribute(): void {
        //Add estimated delivery attribute
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ItemInterface::ESTIMATED_DELIVERY,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'int',
                'input' => 'select',
                'label' => 'Estimated Delivery',
                'source' => EstimatedDelivery::class,
                'default' => EstimatedDelivery::DELIVERY_AVAILABLE,
                'required' => true,
                'sort_order' => 30,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true
            ]
        );
    }

    /**
     * Create EAN attribute
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function createEanAttribute(): void {
        //Add EAN attribute
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ItemInterface::EAN,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'EAN',
                'required' => false,
                'sort_order' => 1,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true
            ]
        );
    }

    /**
     * Create Season attribute
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function createSeasonAttribute(): void {
        //Add Season attribute
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ItemInterface::SEASON,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'int',
                'input' => 'select',
                'label' => 'Season',
                'source' => Table::class,
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true
            ]
        );
    }
}
