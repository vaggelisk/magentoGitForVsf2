<?php
/**
 * ItemDataConverter
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Quote;

use Netsteps\Marketplace\Api\Data\MerchantDataInterface;
use Netsteps\Marketplace\Model\Product\Attribute\Source\EstimatedDelivery;
use Netsteps\MarketplaceSales\Traits\ProductDataManagementTrait;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;

/**
 * Class ItemDataConverter
 * @package Netsteps\MarketplaceSales\Model\Quote
 */
class ItemDataConverter implements ItemDataConverterInterface
{
    use ProductDataManagementTrait;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @param SellerRepository $sellerRepository
     */
    public function __construct(SellerRepository $sellerRepository)
    {
        $this->_sellerRepository = $sellerRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertObjectToArray(\Magento\Quote\Api\Data\CartItemInterface $cartItem): array
    {
        $data = [];
        $sellerInfo = $this->getSellerInfoFromQuoteItem($cartItem);
        $sellerId = $this->getSellerIdFromQuoteItem($cartItem);

        if (empty($sellerInfo) || !$sellerId){
            return $data;
        }

        $seller =  $this->_sellerRepository->getById($sellerId);
        $deliveryId = @$sellerInfo[MerchantDataInterface::DELIVERY_ID];

        return [
            self::SELLER_ID_KEY => $sellerId,
            self::SELLER_NAME_KEY => __('Sold by <strong>%1</strong>', $seller->getName()),
            self::ESTIMATED_DELIVERY_KEY => $deliveryId ? $this->_getDeliveryText($deliveryId) : null
        ];
    }

    /**
     * Get delivery text
     * @param int $deliveryId
     * @return string|null
     */
    private function _getDeliveryText(int $deliveryId): ?string {
        $deliveryText = match($deliveryId){
            EstimatedDelivery::DELIVERY_AVAILABLE => '1 - 3',
            EstimatedDelivery::DELIVERY_FOUR_TO_SIX => '4 - 6',
            default => null
        };

        return $deliveryText ? __('Delivery <strong>%1 days</strong>', $deliveryText) : null;
    }
}
