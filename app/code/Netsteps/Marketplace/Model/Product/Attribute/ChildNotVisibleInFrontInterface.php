<?php

namespace Netsteps\Marketplace\Model\Product\Attribute;

use Magento\Catalog\Api\Data\ProductInterface;

interface ChildNotVisibleInFrontInterface
{
    /**
     * @param ProductInterface[] $products
     * @param string $distributorSource
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setChildAsNotVisibleInFront(array $products, string $distributorSource): void;
}
