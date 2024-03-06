<?php
/**
 * Variation
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Item;

use Magento\Framework\DataObject;

/**
 * Class Variation
 * @package Netsteps\Marketplace\Model\Feed\Item
 */
class Variation extends DataObject implements VariationInterface
{
    /**
     * @inheritDoc
     */
    public function getQty(): int
    {
        return (int)$this->_getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): ?float
    {
        $price = $this->_getData(self::PRICE);
        return !is_null($price) ? (float)$price : null;
    }

    /**
     * @inheritDoc
     */
    public function getSpecialPrice(): ?float
    {
        $price = $this->_getData(self::SPECIAL_PRICE);
        return !is_null($price) ? (float)$price : null;    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->_getData(self::ATTRIBUTES) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getEan(): ?string
    {
        return $this->_getData(self::EAN);
    }

    /**
     * @inheritDoc
     */
    public function setQty(int $qty): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface
    {
        if(0 > $qty){
            return $this;
        }

        return $this->setData(self::QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function setPrice(?float $price): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface
    {
        if (!is_null($price) && $price <= 0){
            return $this;
        }

        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function setSpecialPrice(?float $price): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface
    {
        if (!is_null($price) && $price <= 0){
            return $this;
        }

        return $this->setData(self::SPECIAL_PRICE, $price);    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $attributes): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface
    {
        return $this->setData(self::ATTRIBUTES, $attributes);
    }

    /**
     * @inheritDoc
     */
    public function setEan(?string $ean): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface
    {
        return $this->setData(self::EAN, $ean);
    }
}
