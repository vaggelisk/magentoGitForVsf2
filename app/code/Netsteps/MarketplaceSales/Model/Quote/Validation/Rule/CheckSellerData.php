<?php
/**
 * CheckSellerData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Quote\Validation\Rule;

use Magento\Framework\Phrase;
use Magento\Framework\Validation\ValidationResult;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ValidationRules\QuoteValidationRuleInterface;
use Magento\Framework\Validation\ValidationResultFactory;
use Netsteps\Marketplace\Api\Data\MerchantDataInterface;
use Netsteps\Marketplace\Api\ProductIndexRepositoryInterface as ProductIndexRepository;
use Magento\Framework\UrlInterface as Url;

/**
 * Class CheckSellerData
 * @package Netsteps\MarketplaceSales\Model\Quote\Validation\Rule
 */
class CheckSellerData implements QuoteValidationRuleInterface
{
    /**
     * @var ValidationResultFactory
     */
    private ValidationResultFactory $_validationResultFactory;

    /**
     * @var ProductIndexRepository
     */
    private ProductIndexRepository $_productIndexRepository;

    /**
     * @var Url
     */
    private Url $_url;

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param ProductIndexRepository $productIndexRepository
     * @param Url $url
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        ProductIndexRepository $productIndexRepository,
        Url $url
    )
    {
        $this->_validationResultFactory = $validationResultFactory;
        $this->_productIndexRepository = $productIndexRepository;
        $this->_url = $url;
    }

    /**
     * @inheritDoc
     */
    public function validate(Quote $quote): array
    {
        if ($quote->getData('is_split')){
            return [];
        }

        $validationErrors = [];
        $itemCollection = $quote->getItemsCollection();

        foreach ($quote->getAllVisibleItems() as $item) {
            $qty = $item->getQty();

            /** @var  $childItem \Magento\Quote\Model\Quote\Item */
            $childItem = $itemCollection->getItemByColumnValue('parent_item_id', $item->getItemId()) ?? $item;
            $sellerData = $this->_productIndexRepository->getBestSellerDataByProductId($childItem->getProductId());

            if (!$sellerData) {
                $validationErrors[] = __('Product %1 is no longer sold by any seller.', [$item->getName()]);
            } else  {
                $error = $this->validateItemBySellerData($childItem, $sellerData, $qty, $item->getName());

                if ($error){
                    $error .= ' ' . __('Proceed to the <a href="%1" data-role="error-cart-link">cart page</a> to continue.', $this->getCartLink());
                    $validationErrors[] = $error;
                }
            }

            if (!empty($validationErrors)){
                break;
            }
        }

        return [$this->_validationResultFactory->create([
            'errors' => $validationErrors
        ])];
    }

    /**
     * Validate item by current best merchant data
     * @param CartItemInterface $item
     * @param MerchantDataInterface $merchantData
     * @param float $qty
     * @param string|null $productName
     * @return Phrase|null
     */
    private function validateItemBySellerData(
        CartItemInterface $item,
        MerchantDataInterface $merchantData,
        float $qty,
        ?string $productName = null
    ): ?Phrase {
        /** @var  $item \Magento\Quote\Model\Quote\Item */
        $buyRequest = $item->getBuyRequest();
        $sellerId = $buyRequest->getSellerId();
        $productName = $productName ?? $item->getName();

        $sellerId = 1;

        if (!$sellerId){
            return __('There is no related seller for product %1.',[$productName]);
        }

        if ((int)$sellerId !== $merchantData->getSellerId()){
            return __('The product %1 is now sold by a different seller.', $productName);
        }

        if ($merchantData->getQuantity() < $qty){
            return __('The requested quantity for the product %1 is not available from the seller.', $productName);
        }

        return null;
    }

    /**
     * Get cart link
     * @return string
     */
    private function getCartLink(): string
    {
        return $this->_url->getUrl('checkout/cart');
    }
}
