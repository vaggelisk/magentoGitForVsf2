<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Netsteps\Seller\Api\Data\SellerInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.6', '<')){
            $this->createDistributorsEavAttribute($eavSetup);
        }

        $setup->endSetup();
    }

    /**
     * @param EavSetup $eavSetup
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function createDistributorsEavAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            SellerInterface::DISTRIBUTOR_CODE,
            [
                'type'     => 'int',
                'label'    => __('Distributor'),
                'input'    => 'select',
                'source'   => \Netsteps\Seller\Model\Config\DistributorsOptionsSource::class,
                'visible'  => true,
                'required' => false,
                'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'    => 'General',
            ]
        );
    }
}
