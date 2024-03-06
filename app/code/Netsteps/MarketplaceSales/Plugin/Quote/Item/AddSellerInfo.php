<?php
/**
 * SellerProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Quote\Item;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\ProductOptionExtensionFactory;
use Magento\Quote\Model\Quote\ProductOptionFactory;
use Netsteps\MarketplaceSales\Traits\ProductDataManagementTrait;

/**
 * Class SellerProcessor
 * @package Netsteps\MarketplaceSales\Model\Quote\Item
 */
class AddSellerInfo
{
    use ProductDataManagementTrait;

    private array $quotes = [];

    /**
     * @var ProductOptionFactory
     */
    private ProductOptionFactory $_productOptionFactory;

    /**
     * @var ProductOptionExtensionFactory
     */
    private ProductOptionExtensionFactory $_extensionFactory;

    /**
     * @param ProductOptionFactory $productOptionFactory
     * @param ProductOptionExtensionFactory $extensionFactory
     */
    public function __construct(
        ProductOptionFactory $productOptionFactory,
        ProductOptionExtensionFactory $extensionFactory,
    ) {
        $this->_productOptionFactory = $productOptionFactory;
        $this->_extensionFactory = $extensionFactory;
    }

    /**
     * Apply seller info as extension attributes in cart item options
     * @param \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $cartItemOptionsProcessor
     * @param CartItemInterface $resultItem
     * @param string $productType
     * @param CartItemInterface $cartItem
     * @return CartItemInterface
     */
    public function afterAddProductOptions(
        \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $cartItemOptionsProcessor,
        CartItemInterface $resultItem,
        string $productType,
        CartItemInterface $cartItem
    ): CartItemInterface {
        $quote = $this->getQuote($resultItem);
        $infoItem = $quote->getItemsCollection()
                ->getItemByColumnValue('parent_item_id', $resultItem->getItemId()) ?? $resultItem;
        $sellerInfo = $this->getSellerInfoFromQuoteItem($infoItem);

        if (empty($sellerInfo)){
            return $resultItem;
        }

        $this->applySellerInfo($resultItem, $sellerInfo);
        return $resultItem;
    }

    /**
     * Get quote for cart item (use this method to cache the quotes)
     * @param CartItemInterface $cartItem
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuote(CartItemInterface $cartItem): \Magento\Quote\Model\Quote {
        if (isset($this->quotes[$cartItem->getQuoteId()])) {
            return $this->quotes[$cartItem->getQuoteId()];
        }

        $this->quotes[$cartItem->getQuoteId()] = $cartItem->getQuote();
        return $this->quotes[$cartItem->getQuoteId()];
    }

    /**
     * Apply seller info extension attributes to product options
     * @param CartItemInterface $cartItem
     * @param array $sellerInfo
     * @return void
     */
    private function applySellerInfo(CartItemInterface $cartItem, array $sellerInfo): void {
        /** @var  $productOption \Magento\Quote\Model\Quote\ProductOption */
        $productOption = $cartItem->getProductOption() ? : $this->_productOptionFactory->create();

        /** @var  \Magento\Quote\Api\Data\ProductOptionExtensionInterface $extensibleAttribute */
        $extensionAttributes = $productOption->getExtensionAttributes()
            ? $productOption->getExtensionAttributes()
            : $this->_extensionFactory->create();

        $extensionAttributes->setSellerId((int)$sellerInfo['seller_id']);
        $extensionAttributes->setDeliveryId((int)$sellerInfo['delivery_id']);

        $productOption->setExtensionAttributes($extensionAttributes);
        $cartItem->setProductOption($productOption);
    }
}
