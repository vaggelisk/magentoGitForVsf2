<?php
/**
 * Data
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Item\Email;

use Magento\Framework\DataObject;

/**
 * Class Data
 * @package Netsteps\MarketplaceSales\Model\Order\Item\Email
 */
class Data extends DataObject
{
    /**
     * Set seller name
     * @param string|null $sellerName
     * @return $this
     */
    public function setSellerName(?string $sellerName): self {
        return $this->setData('seller_name', $sellerName);
    }

    /**
     * Get seller name
     * @return string|null
     */
    public function getSellerName(): ?string {
        return $this->_getData('seller_name');
    }

    /**
     * Set estimated delivery label
     * @param string|null $estimatedDelivery
     * @return $this
     */
    public function setEstimatedDelivery(?string $estimatedDelivery): self {
        return $this->setData('estimated_delivery', $estimatedDelivery);
    }

    /**
     * Get estimated delivery label
     * @return string|null
     */
    public function getEstimatedDelivery(): ?string {
        return $this->_getData('estimated_delivery');
    }

    /**
     * Set item special price
     * @param float|null $price
     * @return $this
     */
    public function setSpecialPrice(?float $price): self {
        return $this->setData('special_price', $price);
    }

    /**
     * Get item special price
     * @return float|null
     */
    public function getSpecialPrice(): ?float {
        return $this->getData('special_price');
    }

    /**
     * Set item price
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): self {
        return $this->setData('price', $price);
    }

    /**
     * Get item price
     * @return float|null
     */
    public function getPrice(): ?float {
        return $this->getData('price');
    }
}
