<?php
/**
 * VariationInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Item;

/**
 * Interface VariationInterface
 * @package Netsteps\Marketplace\Model\Feed\Item
 */
interface VariationInterface
{
    const QTY = 'qty';
    const PRICE = 'price';
    const SPECIAL_PRICE = 'special_price';
    const ATTRIBUTES = 'attributes';
    const EAN = 'ean';

    /**
     * Get variation quantity
     * @return int
     */
    public function getQty(): int;

    /**
     * Get variation price
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * Get variation special price
     * @return float|null
     */
    public function getSpecialPrice(): ?float;

    /**
     * Get variation's attribute list
     * Attributes => array (
     *      attribute_code => attribute_option_id
     *      ....
     * )
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Get ean number for variation (child product)
     * @return string|null
     */
    public function getEan(): ?string;

    /**
     * Set variation quantity
     * @param int $qty
     * @return VariationInterface
     */
    public function setQty(int $qty): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface;

    /**
     * Set variation price
     * @param float|null $price
     * @return VariationInterface
     */
    public function setPrice(?float $price): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface;

    /**
     * Set variation special price
     * @param float|null $price
     * @return VariationInterface
     */
    public function setSpecialPrice(?float $price): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface;

    /**
     * Set variation attribute list
     * Attributes => array (
     *      attribute_code => attribute_option_id
     *      ....
     * )
     * @param array $attributes
     * @return VariationInterface
     */
    public function setAttributes(array $attributes): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface;

    /**
     * Set ean number for variation
     * @param string|null $ean
     * @return VariationInterface
     */
    public function setEan(?string $ean): \Netsteps\Marketplace\Model\Feed\Item\VariationInterface;
}
