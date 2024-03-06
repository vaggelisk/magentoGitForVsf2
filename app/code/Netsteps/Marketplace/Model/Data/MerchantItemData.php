<?php
/**
 * MerchantItemData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Data;

use Magento\Catalog\Model\Product\Type as BaseProductType;
use Magento\Framework\DataObject;
use Netsteps\Marketplace\Api\Data\MerchantDataInterface;

/**
 * Class MerchantItemData
 * @package Netsteps\Marketplace\Model\Data
 */
class MerchantItemData extends DataObject implements MerchantDataInterface
{
    /**
     * @inheritDoc
     */
    public function getProductId(): int
    {
        return $this->_getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId(): int
    {
        return $this->_getData(self::SELLER_ID);
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
    public function getPrice(): float
    {
        return $this->_getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getSpecialPrice(): ?float
    {
        return $this->_getData(self::SPECIAL_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryId(): int
    {
        return $this->_getData(self::DELIVERY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getFinalPrice(): float
    {
        if ($specialPrice = $this->getSpecialPrice()) {
            return min($specialPrice, $this->getPrice());
        }
        return $this->getPrice();
    }

    /**
     * @inheritDoc
     */
    public function getSourceCode(): ?string
    {
        return $this->_getData(self::SOURCE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getQuantity(): ?int
    {
        $qty = $this->_getData(self::QUANTITY);
        return $qty ? (int)$qty : null;
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableProductTypesToIndex(): array
    {
        return [
            BaseProductType::TYPE_SIMPLE,
            BaseProductType::TYPE_VIRTUAL
        ];
    }
}
