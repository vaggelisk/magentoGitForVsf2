<?php
/**
 * CartItemOptionProcessorPlugin
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Quote\Item;

use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartItemInterface;
use Netsteps\MarketplaceSales\Exception\Quote\ValidationException;
use Netsteps\MarketplaceSales\Model\Product\SellerManagementInterface;

/**
 * Class CartItemOptionProcessorPlugin
 * @package Netsteps\MarketplaceSales\Plugin\Quote\Item
 */
class CartItemOptionProcessorPlugin
{
    /**
     * Plugin to pass seller id from cart item extension attributes to
     * infoBuy request object
     *
     * @param \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $processor
     * @param float|DataObject|null $result
     * @param $productType
     * @param CartItemInterface $cartItem
     * @return DataObject|float|null
     */
    public function afterGetBuyRequest(
        \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $processor,
        $result,
        $productType,
        CartItemInterface $cartItem
    )
    {
        if ($sellerId = $this->exportSellerId($cartItem)) {
            if (!$result instanceof DataObject) {
                $rawResult = $result;
                $result = new DataObject();

                if (is_float($rawResult)) {
                    $result->setData('qty', $rawResult);
                }
            }

            $result->setData(SellerManagementInterface::INFO_BUY_REQUEST_SELLER_ID, (int)$sellerId);
        }

        return $result;
    }

    /**
     * Export seller id from cart item
     * @param CartItemInterface $cartItem
     * @return int|null
     */
    private function exportSellerId(CartItemInterface $cartItem): ?int {
        $sellerId = null;

        if ($cartItem->getExtensionAttributes()) {
            $sellerId = $cartItem->getExtensionAttributes()->getSellerId();
        }

        if (!$sellerId && $cartItem->getProductOption() && $cartItem->getProductOption()->getExtensionAttributes()) {
            $sellerId = $cartItem->getProductOption()->getExtensionAttributes()->getSellerId();
        }

        return $sellerId ? (int)$sellerId : null;
    }
}
