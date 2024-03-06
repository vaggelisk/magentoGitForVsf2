<?php
/**
 * ProductDataManagementTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Traits;

use Netsteps\Marketplace\Model\Feed\ItemInterface;

/**
 * Trait ProductDataManagementTrait
 * @package Netsteps\MarketplaceSales\Traits
 */
trait ProductDataManagementTrait
{
    /**
     * Get product distributor id
     * @param \Magento\Catalog\Model\Product $product
     * @return int|null
     */
    public function getProductDistributorId(\Magento\Catalog\Model\Product $product): ?int {
        if(!$product->hasData(ItemInterface::DISTRIBUTOR_ID)) {
            return null;
        }
        return (int)$product->getData(ItemInterface::DISTRIBUTOR_ID);
    }

    /**
     * Extract seller info from quote item
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    public function getSellerInfoFromQuoteItem(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item): array {
        $infoBuyRequestData = $this->getBuyInfoRequestData($item);
        return $infoBuyRequestData['seller_info'] ?? [];
    }

    /**
     * Extract seller info from quote item
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return int|null
     */
    public function getSellerIdFromQuoteItem(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item): ?int {
        $infoBuyRequestData = $this->getBuyInfoRequestData($item);
        return isset($infoBuyRequestData['seller_id']) ? (int)$infoBuyRequestData['seller_id'] : null;
    }

    /**
     * Extract info buy request data
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    private function getBuyInfoRequestData(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item): array {
        $infoBuyRequestRaw = $item->getOptionByCode('info_buyRequest');

        if (!$infoBuyRequestRaw) {
            return [];
        }

        $infoBuyRequestData = @json_decode($infoBuyRequestRaw->getValue(), true);
        return $infoBuyRequestData ?? [];
    }
}
