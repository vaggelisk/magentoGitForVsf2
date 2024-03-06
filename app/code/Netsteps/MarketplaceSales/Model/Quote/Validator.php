<?php
/**
 * Validator
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Quote;

use Netsteps\Marketplace\Api\ProductIndexRepositoryInterface as ProductIndexRepository;
use Netsteps\MarketplaceSales\Api\QuoteValidatorInterface;
use Netsteps\MarketplaceSales\Exception\OutOfStockException;
use Netsteps\MarketplaceSales\Traits\ProductDataManagementTrait;

/**
 * Class Validator
 * @package Netsteps\MarketplaceSales\Model\Quote
 */
class Validator implements QuoteValidatorInterface
{
    use ProductDataManagementTrait;

    /**
     * @var ProductIndexRepository
     */
    private ProductIndexRepository $_productIndexRepository;

    /**
     * @param ProductIndexRepository $productIndexRepository
     */
    public function __construct(ProductIndexRepository $productIndexRepository)
    {
        $this->_productIndexRepository = $productIndexRepository;
    }

    /**
     * @inheritDoc
     * @throws OutOfStockException
     */
    public function validate(\Magento\Quote\Api\Data\CartInterface $quote, array $itemsData): void
    {
        $searchData = $this->prepareSearchData($quote, $itemsData);

        if (!empty($searchData)) {
            $errors = [];
            $processedIds = [];
            $productIds = array_keys($searchData);

            foreach ($this->_productIndexRepository->getProductsBestOfferSeller($productIds) as $allSellerData) {
                /** @var  $sellerData \Netsteps\Marketplace\Api\Data\MerchantDataInterface */
                foreach ($allSellerData as $sellerData) {
                    $productId = $sellerData->getProductId();
                    $qty = $searchData[$productId]['qty'];
                    $sellerId = $searchData[$productId]['seller_id'];

                    if (!$this->isValidSellerData($sellerData, $sellerId, $qty)) {
                        $errors[] = $searchData[$productId]['name'];
                    }

                    $processedIds[] = $productId;
                }
            }

            $productIdsWithoutOffer = array_diff($productIds, $processedIds);

            foreach ($productIdsWithoutOffer as $productIdWithoutOffer) {
                $errors[] = $searchData[$productIdWithoutOffer]['name'];
            }

            if (!empty($errors)) {
                $productWord = count($errors) === 1 ? 'product' : 'products';
                $message = "Requested quantity for {$productWord} %1 does not exist";
                throw new OutOfStockException(__($message, implode(', ' , $errors)));
            }
        }
    }

    /**
     * Check if is valid merchant data
     * @param \Netsteps\Marketplace\Api\Data\MerchantDataInterface $merchantData
     * @param int $sellerId
     * @param int $qty
     * @return bool
     */
    private function isValidSellerData(
        \Netsteps\Marketplace\Api\Data\MerchantDataInterface $merchantData,
        int $sellerId,
        int $qty
    ): bool
    {
        return $merchantData->getSellerId() === $sellerId &&
            $merchantData->getQuantity() >= $qty;
    }

    /**
     * Prepare search data
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $updateData
     * @return array
     */
    private function prepareSearchData(\Magento\Quote\Model\Quote $quote, array $updateData): array
    {
        $data = [];
        $itemCollection = $quote->getItemsCollection();

        foreach ($updateData as $itemId => $itemData) {
            $qty = $itemData['qty'] ?? null;
            if (is_null($qty)) {
                continue;
            }

            $qty = (int)($qty);
            if (isset($itemData['remove']) || $qty === 0) {
                continue;
            }

            /** @var  $item \Magento\Quote\Api\Data\CartItemInterface */
            $item = $itemCollection->getItemByColumnValue('parent_item_id', $itemId) ??
                $itemCollection->getItemById($itemId);
            $parentItem = $item->getParentItemId() ? $item->getParentItem() : null;

            $sellerId = $this->getSellerIdFromQuoteItem($item);
            if (!$sellerId) {
                continue;
            }

            $data[$item->getProduct()->getId()] = [
                'seller_id' => (int)$sellerId,
                'qty' => $qty,
                'name' => $parentItem ? $parentItem->getName() : $item->getName()
            ];
        }

        return $data;
    }
}
