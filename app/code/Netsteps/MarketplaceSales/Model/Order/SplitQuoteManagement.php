<?php
/**
 * SplitQuoteManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;


use Magento\Quote\Model\Cart\CustomerCartResolver;
use Magento\Quote\Model\GuestCart\GuestCartResolver;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface as CartRepository;

/**
 * Class SplitQuoteManagement
 * @package Netsteps\MarketplaceSales\Model\Order
 */
class SplitQuoteManagement implements SplitQuoteManagementInterface
{
    /**
     * Keep registration of carts to resolve
     * @var array
     */
    private array $registries = [];

    /**
     * @var CustomerCartResolver
     */
    private CustomerCartResolver $_customerCartResolver;

    /**
     * @var GuestCartResolver
     */
    private GuestCartResolver $_guestCartResolver;

    /**
     * @var QuoteIdMaskFactory
     */
    private QuoteIdMaskFactory $_quoteIdMaskFactory;

    /**
     * @var CartRepository
     */
    private CartRepository $_cartRepository;

    /**
     * @param CustomerCartResolver $cartResolver
     * @param GuestCartResolver $guestCartResolver
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepository $cartRepository
     */
    public function __construct(
        CustomerCartResolver $cartResolver,
        GuestCartResolver    $guestCartResolver,
        QuoteIdMaskFactory   $quoteIdMaskFactory,
        CartRepository       $cartRepository
    )
    {
        $this->_customerCartResolver = $cartResolver;
        $this->_guestCartResolver = $guestCartResolver;
        $this->_quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_cartRepository = $cartRepository;
    }

    /**
     * @inheritDoc
     */
    public function prepareQuote(\Magento\Quote\Model\Quote $quote): void
    {
        if (!$quote->hasItems()) {
            return;
        }

        $this->registries[] = $this->prepareQuoteData($quote);
        foreach ($quote->getAllVisibleItems() as $item) {
            $quote->deleteItem($item);
        }
        $quote->getItemsCollection()->save();
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolve(): void
    {
        foreach ($this->registries as $registry) {
            $items = $registry['items'];

            if (count($items) === 0){
                continue;
            }

            $quote = $this->createQuote($registry);

            if (!$quote) {
                return;
            }

            foreach ($items as $itemData) {
                /** @var  $product \Magento\Catalog\Model\Product */
                $product = clone $itemData['product'];
                $product->addCustomOption('preserved_item_id', $itemData['preserved_item_id']);
                $quote->addProduct($product, $itemData['request']);
            }

            $quote->collectTotals();
            $this->_cartRepository->save($quote);
        }
    }

    /**
     * Create a new quote based on registry data given
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function createQuote(array $registry): ?\Magento\Quote\Model\Quote {
        if ($registry['is_guest']) {
            $maskId = $registry['mask_id'] ?? false;
            $email = $registry['email'] ?? false;

            if ($maskId) {
                /** @var  $maskedId QuoteIdMask */
                $maskedId = $this->_quoteIdMaskFactory->create();
                $maskedId->load($maskId, 'masked_id');

                if ($maskedId->getEntityId()) {
                    $quote = $this->_guestCartResolver->resolve($maskId);

                    if ($email){
                        $quote->setCustomerEmail($email);
                    }

                    return $quote;
                }
            }
        } elseif ($registry['customer_id']) {
            return $this->_customerCartResolver->resolve($registry['customer_id']);
        }

        return null;
    }

    /**
     * Prepare basic quote data
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    private function prepareQuoteData(\Magento\Quote\Model\Quote $quote): array
    {
        $quoteData = [
            'is_guest' => $quote->getCustomerIsGuest(),
            'customer_id' => $quote->getCustomerId(),
            'email' => $quote->getCustomerEmail(),
            'items' => $this->prepareItems($quote)
        ];

        if ($quote->getCustomerIsGuest()) {
            /** @var QuoteIdMask $quoteMask */
            $quoteMask = $this->_quoteIdMaskFactory->create();
            $quoteMask->load($quote->getId(), 'quote_id');

            if ($maskId = $quoteMask->getMaskedId()) {
                $quoteData['mask_id'] = $maskId;
            }
        }

        return $quoteData;
    }

    /**
     * Prepare items
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    private function prepareItems(\Magento\Quote\Model\Quote $quote): array
    {
        $items = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $items[] = [
                'product' => $item->getProduct(),
                'request' => $item->getBuyRequest(),
                'preserved_item_id' => $item->getId()
            ];
        }

        return $items;
    }
}
