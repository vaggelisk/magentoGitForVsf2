<?php
/**
 * Item
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

use Magento\Framework\DataObject;

/**
 * Class Item
 * @package Netsteps\Marketplace\Model\Feed
 */
class Item extends DataObject implements ItemInterface
{
    /**
     * @inheritDoc
     */
    public function getSku(): string
    {
        return $this->_getData(self::SKU);
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
    public function getName(): string
    {
        return $this->_getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): float
    {
        return (float)$this->_getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getSpecialPrice(): ?float
    {
        return $this->_getData(self::SPECIAL_PRICE) ?
            (float)$this->_getData(self::SPECIAL_PRICE) : null;
    }

    /**
     * @inheritDoc
     */
    public function getColor(): ?string
    {
        return $this->_getData(self::COLOR);
    }

    /**
     * @inheritDoc
     */
    public function getStock(): int
    {
        $stock = (int)$this->_getData(self::STOCK);
        return max($stock, 0);
    }

    /**
     * @inheritDoc
     */
    public function getIsInStock(): string
    {
        $stockFlag = $this->_getData(self::IS_IN_STOCK);

        if (!$stockFlag || !in_array($stockFlag, [self::IN_STOCK_FLAG, self::OUT_OF_STOCK_FLAG])){
            $stockFlag = $this->getStock() > 0 ? self::IN_STOCK_FLAG : self::OUT_OF_STOCK_FLAG;
        }

        return $stockFlag;
    }

    /**
     * @inheritDoc
     */
    public function getEstimatedDelivery(): ?int
    {
        return $this->_getData(self::ESTIMATED_DELIVERY) ?
            (int)$this->_getData(self::ESTIMATED_DELIVERY) : null;
    }

    /**
     * @inheritDoc
     */
    public function setSku(string $sku): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(?string $description): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function setPrice(float $price): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        if ($price < 0){
            return $this;
        }

        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function setSpecialPrice(?float $price): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::SPECIAL_PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function setColor(string $color): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::COLOR, $color);
    }

    /**
     * @inheritDoc
     */
    public function setStock(int $stock): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::STOCK, $stock);
    }

    /**
     * @inheritDoc
     */
    public function setIsInStock(string $stockStatus): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::IS_IN_STOCK, $stockStatus);
    }

    /**
     * @inheritDoc
     */
    public function setEstimatedDelivery(?int $deliveryId): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::ESTIMATED_DELIVERY, $deliveryId);
    }

    /**
     * @inheritDoc
     */
    public function getMpn(): string
    {
        return $this->_getData(self::MPN);
    }

    /**
     * @inheritDoc
     */
    public function getImage(): string
    {
        return $this->_getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function getBrand(): ?string
    {
        return $this->_getData(self::BRAND);
    }

    /**
     * @inheritDoc
     */
    public function getSeason(): ?string
    {
        return $this->_getData(self::SEASON);
    }

    /**
     * @inheritDoc
     */
    public function getRetailPrice(): float
    {
        return (float)$this->_getData(self::RETAIL_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalImages(): array
    {
        if (!is_array($this->_getData(self::ADDITIONAL_IMAGES))) {
            return [];
        }

        return $this->_getData(self::ADDITIONAL_IMAGES) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getVariations(): array
    {
        return $this->_getData(self::VARIATIONS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getWeight(): float
    {
        return (float)$this->_getData(self::WEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setMpn(string $mpn): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::MPN, $mpn);
    }

    /**
     * @inheritDoc
     */
    public function setEan(?string $ean): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::EAN, $ean);
    }

    /**
     * @inheritDoc
     */
    public function setImage(string $image): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @inheritDoc
     */
    public function setBrand(string $brand): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::BRAND, $brand);
    }

    /**
     * @inheritDoc
     */
    public function setSeason(?string $season): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::SEASON, $season);
    }

    /**
     * @inheritDoc
     */
    public function setRetailPrice(float $price): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        if ($price <= 0){
            return $this;
        }
        return $this->setData(self::RETAIL_PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalImages(array $images): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::ADDITIONAL_IMAGES, $images);
    }

    /**
     * @inheritDoc
     */
    public function setVariations(array $variations): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::VARIATIONS, $variations);
    }

    /**
     * @inheritDoc
     */
    public function setWeight(?float $weight): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * @inheritDoc
     */
    public function getCategories(): array
    {
        $categories = (string)$this->_getData(self::CATEGORIES);
        return explode(',', $categories);
    }

    /**
     * @inheritDoc
     */
    public function setCategories(string $categories): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::CATEGORIES, $categories);
    }

    /**
     * @inheritDoc
     */
    public function getSizeInfo(): ?string
    {
        return $this->_getData(self::SIZE_INFO);
    }

    /**
     * @inheritDoc
     */
    public function getCompositionInfo(): ?string
    {
        return $this->_getData(self::COMPOSITION_INFO);
    }

    /**
     * @inheritDoc
     */
    public function setSizeInfo(?string $html): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::SIZE_INFO, $html);
    }

    /**
     * @inheritDoc
     */
    public function setCompositionInfo(?string $html): \Netsteps\Marketplace\Model\Feed\ItemInterface
    {
        return $this->setData(self::COMPOSITION_INFO, $html);
    }
}
