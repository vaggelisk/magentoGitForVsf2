<?php
/**
 * UpgradeData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Setup;


use JetBrains\PhpStorm\Pure;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface;
use Netsteps\MarketplaceSales\Api\ProductManagementInterface;
use Netsteps\MarketplaceSales\Model\Product\Metadata;

/**
 * Class UpgradeData
 * @package Netsteps\MarketplaceSales\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var StatusFactory
     */
    private StatusFactory $_statusFactory;

    /**
     * @var StatusResource
     */
    private StatusResource $_statusResource;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $_eavSetupFactory;

    /**
     * @var EavSetup|null
     */
    private ?EavSetup $_eavSetup = null;

    /**
     * @param StatusFactory $statusFactory
     * @param StatusResource $statusResource
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResource $statusResource,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->_statusFactory = $statusFactory;
        $this->_statusResource = $statusResource;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->_eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        if(version_compare($context->getVersion(), '1.0.7', '<')) {
            $this->createPendingApprovalStatus();;
            $this->createApprovedStatus();
        }

        if(version_compare($context->getVersion(), '1.0.8', '<')) {
            $this->createProductSellerAttributes();
        }

        if(version_compare($context->getVersion(), '1.0.9', '<')) {
            $this->createDiscountSellerAttribute();
        }

        if(version_compare($context->getVersion(), '1.0.10', '<')) {
            $this->createCustomVisibilityAttribute();
        }

        if(version_compare($context->getVersion(), '1.0.12', '<')){
            $this->createDeclineStatus();
        }

        $setup->endSetup();
    }

    /**
     * Create pending_approval order status
     * @throws \Exception
     */
    private function createPendingApprovalStatus(): void {
        /** @var  $status Status */
        $status = $this->_statusFactory->create();
        $status->setData([
            'status' => OrderStatusManagementInterface::STATUS_PENDING_APPROVAL,
            'label' => 'Pending Approval'
        ]);

        try {
            $this->_statusResource->save($status);
        } catch (AlreadyExistsException $e) {
            return;
        }

        $status->assignState(Order::STATE_PROCESSING, false, true);
        $status->assignState(Order::STATE_NEW, false, true);
    }

    /**
     * Create approved order status
     * @throws \Exception
     */
    private function createApprovedStatus(): void {
        /** @var  $status Status */
        $status = $this->_statusFactory->create();
        $status->setData([
            'status' => OrderStatusManagementInterface::STATUS_APPROVED,
            'label' => 'Approved'
        ]);

        try {
            $this->_statusResource->save($status);
        } catch (AlreadyExistsException $e) {
            return;
        }

        $status->assignState(Order::STATE_PROCESSING, false, true);
    }

    /**
     * Create approved order status
     * @throws \Exception
     */
    private function createDeclineStatus(): void {
        /** @var  $status Status */
        $status = $this->_statusFactory->create();
        $status->setData([
            'status' => OrderStatusManagementInterface::STATUS_DECLINED,
            'label' => 'Declined'
        ]);

        try {
            $this->_statusResource->save($status);
        } catch (AlreadyExistsException $e) {
            return;
        }

        $status->assignState(Order::STATE_CLOSED, false, true);
        $status->assignState(Order::STATE_CANCELED, false, true);
    }

    /**
     * Create product attributes to keep all necessary seller related data to the product
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function createProductSellerAttributes(): void {
        /** Create an attribute for the lowest seller id */
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ProductManagementInterface::LOWEST_SELLER_ID,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'int',
                'input' => 'select',
                'label' => 'Best Offer Seller',
                'required' => false,
                'sort_order' => 200,
                'source' => \Netsteps\Seller\Model\Config\SellersAttributeOptionsSource::class,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'apply_to' => $this->getAppliedTypes(),
                'used_in_product_listing' => true,
                'used_for_sort_by' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true
            ]
        );

        /** Create an attribute for the lowest seller data */
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ProductManagementInterface::LOWEST_SELLER_DATA,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'text',
                'input' => 'hidden',
                'label' => 'Best Offer Seller Data',
                'required' => false,
                'sort_order' => 201,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'apply_to' => $this->getAppliedTypes(),
                'used_in_product_listing' => true,
                'used_for_sort_by' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false
            ]
        );
    }

    /**
     * Create lowest seller discount product attribute
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createDiscountSellerAttribute(): void {
        $appliedTypes = $this->getAppliedTypes();

        /** Create an attribute for the lowest seller discount */
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ProductManagementInterface::SELLER_DISCOUNT,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'int',
                'input' => 'text',
                'label' => 'Lowest Seller Discount',
                'required' => false,
                'sort_order' => 202,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'apply_to' => $appliedTypes,
                'frontend_class' => 'validate-digits validate-zero-or-greater',
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true
            ]
        );

    }

    /**
     * Create custom visibility attribute to use it in front end
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createCustomVisibilityAttribute(): void {
        $appliedTypes = $this->getAppliedTypes();

        /** Create an attribute for the lowest seller discount */
        $this->_eavSetup->addAttribute(
            Product::ENTITY,
            ProductManagementInterface::IS_VISIBLE_IN_FRONT,
            [
                'group' => 'Marketplace Additional Attributes',
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'Is Visible in Frontend',
                'required' => false,
                'sort_order' => 1,
                'source_model' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true
            ]
        );

    }

    /**
     * @return string
     */
    #[Pure] private function getAppliedTypes(): string {
        $appliedTypes = Metadata::getProductIndexedTypes();

        if (empty($appliedTypes)){
            $appliedTypes = ['simple'];
        }

        return implode(',', $appliedTypes);
    }
}
