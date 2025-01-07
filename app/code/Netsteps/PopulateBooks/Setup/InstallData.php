<?php
namespace Netsteps\PopulateBooks\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Store;

class InstallData implements InstallDataInterface
{
    private EavSetupFactory $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'mp_new_attribute',
            [
                'type'     => 'int',
                'label'    => 'Your Category Attribute Name',
                'input'    => 'boolean',
                'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible'  => true,
                'default'  => '0',
                'required' => false,
                'global'   => ScopedAttributeInterface::SCOPE_GLOBAL,
//                'type'         => 'int',
//                'label'        => 'Βιβλιοθηκονομικός Αριθμός Min',
//                'input'        => 'textarea',
//                'sort_order'   => 100,
//                'source'       => \Magento\Eav\Model\Entity\Attribute\Source\SpecificSourceInterface::class,
//                'global'       => ScopedAttributeInterface::SCOPE_GLOBAL,
//                'visible'      => true,
//                'required'     => false,
//                'user_defined' => false,
//                'default'      => null,
//                'group'        => '',
                'backend'      => ''
            ]
        );
    }

}
