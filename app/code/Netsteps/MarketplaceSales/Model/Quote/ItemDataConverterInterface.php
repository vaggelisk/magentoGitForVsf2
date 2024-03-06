<?php
/**
 * ItemDataConverterInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Quote;

/**
 * Interface ItemDataConverterInterface
 * @package Netsteps\MarketplaceSales\Model\Quote
 */
interface ItemDataConverterInterface
{
    const SELLER_ID_KEY = 'seller_id';
    const SELLER_NAME_KEY = 'seller_name';
    const ESTIMATED_DELIVERY_KEY = 'estimated_delivery';

    /**
     * Convert quote item object to array
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return array
     */
    public function convertObjectToArray(\Magento\Quote\Api\Data\CartItemInterface $cartItem): array;
}
