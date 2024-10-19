<?php

namespace VendoreName\ModuleName\Model\Resolver\Product;

use Magento\Framework\GraphQl\Query\ResolverInterface;

/**
 * Product custom attribute field resolver
 */
class AddCustomAttribute implements ResolverInterface
{

    /**
     * @var Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->productFactory = $productFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
                                                        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $product = $value['model'];
        $_product = $this->productFactory->create()->load($product->getId());
        return $_product->getData('sample_attribute');
    }
}
