<?php

namespace Netsteps\PopulateBooks\Model\Resolver\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetSubtitle implements ResolverInterface
{
    protected Product $productdata;

    public function __construct(
        Product $productdata
    ) {
        $this->productdata = $productdata;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $product = $value['model'];
        $productdata = $this->productdata->load($product->getId());

        return $productdata->getCustomAttribute('subtitle')->getValue();
    }
}
