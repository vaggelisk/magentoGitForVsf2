<?php
/**
 * ConfigProvider
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Block\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;

use Magento\Checkout\Model\Session as CheckoutSession;
use Netsteps\Marketplace\Model\Data\MerchantItemData;
use Netsteps\MarketplaceSales\Model\Quote\ItemDataConverterInterface as ItemConverter;

/**
 * Class ConfigProvider
 * @package Netsteps\MarketplaceSales\Block\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $_checkoutSession;

    /**
     * @var ItemConverter
     */
    private ItemConverter $_itemConverter;

    /**
     * @param CheckoutSession $checkoutSession
     * @param ItemConverter $converter
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        ItemConverter $converter
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_itemConverter = $converter;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $items = [];
        $sellers = [];

        $availableTypes = MerchantItemData::getAvailableProductTypesToIndex();
        $quote = $this->_checkoutSession->getQuote();
        /** @var  $item \Magento\Quote\Model\Quote\Item */
        foreach ($quote->getAllItems() as $item) {
            if (!in_array($item->getProductType(), $availableTypes)){
                continue;
            }
            $itemData = $this->_itemConverter->convertObjectToArray($item);

            if (empty($itemData)){
                continue;
            }

            $sellers[] = $itemData[ItemConverter::SELLER_ID_KEY];
            unset($itemData[ItemConverter::SELLER_ID_KEY]);

            $itemId = $item->getParentItemId() ?? $item->getItemId();
            $items[$itemId] = $itemData;
        }

        return [
            'marketplace' => [
                'numOfDeliveries' => count(array_unique($sellers)),
                'itemData' => $items
            ]
        ];
    }
}
