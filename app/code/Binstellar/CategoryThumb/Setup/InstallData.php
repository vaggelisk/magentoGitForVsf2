<?php
namespace Binstellar\CategoryThumb\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
/**
�* @codeCoverageIgnore
�*/
class InstallData implements InstallDataInterface
{
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

	public function install(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	) {
		/** @var EavSetup $eavSetup */
    	$setup->startSetup();
    	$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'is_display_on_homepage',
            [
                'type' => 'int',
                'label' => 'Is Display on Homepage?',
                'input' => 'select',
                'sort_order' => 333,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
				'is_html_allowed_on_front' => true,
				'used_in_product_listing' => true, // for category pages
				'visible_on_front' => true, // for frontend??
				'is_used_in_grid' => true, // for category pages
				'is_visible_in_grid' => true // for category pages
            ]
        );

		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'category_thumb_image',
			[
				'type' => 'varchar',
				'label' => 'Category Image',
				'input' => 'image',
				'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
				'required' => false,
				'sort_order' => 10,
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group' => 'General Information',
				'is_html_allowed_on_front' => true,
				'used_in_product_listing' => true, // for category pages
				'visible_on_front' => true, // for frontend??
				'is_used_in_grid' => true, // for category pages
				'is_visible_in_grid' => true // for category pages
			]
		);

        $eavSetup->addAttribute(
             \Magento\Catalog\Model\Category::ENTITY,
            'category_description',
            [
                'type' => 'text',
                'label' => 'Category Description',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
				'is_html_allowed_on_front' => true,
				'used_in_product_listing' => true, // for category pages
				'visible_on_front' => true, // for frontend??
				'is_used_in_grid' => true, // for category pages
				'is_visible_in_grid' => true // for category pages
            ]
        );

		$setup->endSetup();
	}
}
