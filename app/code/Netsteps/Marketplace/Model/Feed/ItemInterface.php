<?php
/**
 * ItemInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

use Netsteps\Marketplace\Model\Feed\Item\VariationInterface;

/**
 * Interface ItemInterface
 * @package Netsteps\Marketplace\Model\Feed
 * @method \Netsteps\Marketplace\Model\Feed\Item setConfigurationAttributes(array $attributes)
 * @method array getConfigurationAttributes()
 * @method \Netsteps\Marketplace\Model\Feed\Item setVariationAttributeCodes(array $attributeCodes)
 * @method array getVariationAttributeCodes()
 * @method \Netsteps\Marketplace\Model\Feed\Item setNsDistributor(int $distributorId)
 * @method int|null getNsDistributor()
 * @method \Netsteps\Marketplace\Model\Feed\Item setStockSourceCode(?string $sourceCode)
 * @method string|null getStockSourceCode()
 */
interface ItemInterface
{
    const IN_STOCK_FLAG = 'Y';
    const OUT_OF_STOCK_FLAG = 'N';

    const NAME = 'name';
    const DESCRIPTION = 'description';
    const SKU = 'sku';
    const MPN = 'mpn';
    const EAN = 'ean';
    const CATEGORIES = 'categories';
    const WEIGHT = 'weight';
    const IMAGE = 'image';
    const BRAND = 'brand';
    const SEASON = 'season';
    const RETAIL_PRICE = 'retail_price';
    const PRICE = 'price';
    const SPECIAL_PRICE = 'special_price';
    const COLOR = 'color';
    const STOCK = 'stock';
    const IS_IN_STOCK = 'is_in_stock';
    const ADDITIONAL_IMAGES = 'additional_images';
    const ESTIMATED_DELIVERY = 'estimated_delivery';
    const VARIATIONS = 'variations';
    const SIZE_INFO = 'size_info';
    const COMPOSITION_INFO = 'composition_info';
    const DISTRIBUTOR_ID = \Netsteps\Seller\Api\Data\SellerInterface::DISTRIBUTOR_CODE;

    //// Getters ////

    /**
     * Get item name
     * @return string
     */
    public function getName(): string;

    /**
     * Get item description
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Get item sku
     * @return string
     */
    public function getSku(): string;

    /**
     * Get item mpn
     * @return string
     */
    public function getMpn(): string;

    /**
     * Get item image
     * @return string
     */
    public function getImage(): string;

    /**
     * Get item brand
     * @return string|null
     */
    public function getBrand(): ?string;

    /**
     * Get season
     * @return string|null
     */
    public function getSeason(): ?string;

    /**
     * Get item retail price
     * @return float
     */
    public function getRetailPrice(): float;

    /**
     * Get item price
     * @return float
     */
    public function getPrice(): float;

    /**
     * Get item special price
     * @return float|null
     */
    public function getSpecialPrice(): ?float;

    /**
     * Get item color
     * @return string|null
     */
    public function getColor(): ?string;

    /**
     * Get stock
     * @return int
     */
    public function getStock(): int;

    /**
     * Get is in stock flag
     * @return string
     */
    public function getIsInStock(): string;

    /**
     * Get item additional images
     * @return array
     */
    public function getAdditionalImages(): array;

    /**
     * Get estimation delivery
     * @return int|null
     */
    public function getEstimatedDelivery(): ?int;

    /**
     * Get product variations (for configurable products)
     * @return VariationInterface[]
     */
    public function getVariations(): array;

    /**
     * Get product weight in kgs
     * @return float
     */
    public function getWeight(): float;

    /**
     * Get product category ids
     * @return array
     */
    public function getCategories(): array;

    /**
     * Get product's size & fit info html
     * @return string|null
     */
    public function getSizeInfo(): ?string;

    /**
     * Get product's composition info html
     * @return string|null
     */
    public function getCompositionInfo(): ?string;

    /**
     * Get ean number for variation
     * @return string|null
     */
    public function getEan(): ?string;

    //// Setters ////

    /**
     * Set item name
     * @param string $name
     * @return ItemInterface
     */
    public function setName(string $name): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item description
     * @param string|null $description
     * @return ItemInterface
     */
    public function setDescription(?string $description): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item sku
     * @param string $sku
     * @return ItemInterface
     */
    public function setSku(string $sku): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item mpn
     * @param string $mpn
     * @return ItemInterface
     */
    public function setMpn(string $mpn): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item main image
     * @param string $image
     * @return ItemInterface
     */
    public function setImage(string $image): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item brand
     * @param string $brand
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setBrand(string $brand): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item season
     * @param string|null $season
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setSeason(?string $season): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item retail price
     * @param float $price
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setRetailPrice(float $price): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item price
     * @param float $price
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setPrice(float $price): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item special_price
     * @param float|null $price
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setSpecialPrice(?float $price): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item color
     * @param string $color
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setColor(string $color): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item stock
     * @param int $stock
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setStock(int $stock): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item stock flag
     * @param string $stockStatus
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setIsInStock(string $stockStatus): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item images
     * @param array $images
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setAdditionalImages(array $images): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set item estimation delivery label
     * @param int|null $deliveryId
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setEstimatedDelivery(?int $deliveryId): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set product variations (for configurable products)
     * @param VariationInterface[] $variations
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setVariations(array $variations): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set product weight in kgs
     * @param float|null $weight
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setWeight(?float $weight): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set product category ids comma separated
     * @param string $categories
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setCategories(string $categories): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set product's size & fit info html
     * @param string|null $html
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setSizeInfo(?string $html): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set product's composition info html
     * @param string|null $html
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface
     */
    public function setCompositionInfo(?string $html): \Netsteps\Marketplace\Model\Feed\ItemInterface;

    /**
     * Set product's ean number
     * @param string|null $ean
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface;
     */
    public function setEan(?string $ean): \Netsteps\Marketplace\Model\Feed\ItemInterface;
}
