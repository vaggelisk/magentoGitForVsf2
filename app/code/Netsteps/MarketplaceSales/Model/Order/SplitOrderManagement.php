<?php
/**
 * SplitOrderManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;


use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Model\Order\Process\Error;
use Netsteps\MarketplaceSales\Model\Product\SellerManagementInterface;
use Netsteps\MarketplaceSales\Traits\OrderDataManagementTrait;
use Netsteps\MarketplaceSales\Model\Order\ShippingProcessorInterface as ShippingProcessor;
use Netsteps\MarketplaceSales\Model\Order\PaymentProcessorInterface as PaymentProcessor;
use Netsteps\MarketplaceSales\Api\OrderProcessErrorRepositoryInterface as ErrorManager;
use Netsteps\MarketplaceSales\Model\Order\InvoiceProcessorInterface as InvoiceProcessor;
use Netsteps\MarketplaceSales\Traits\OrderItemDataManagementTrait;
use Netsteps\MarketplaceSales\Model\Product\StockItemRepositoryInterface as StockItemRepository;
use Netsteps\MarketplaceSales\Model\Inventory\Quote\ValidationRegistry;
use Netsteps\MarketplaceSales\Model\Order\SplitQuoteManagementInterface as SplitQuoteManagement;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Reorder\OrderInfoBuyRequestGetter;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Quote\Api\CartRepositoryInterface as CartRepository;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Quote\Model\QuoteManagement as CartManagement;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Psr\Log\LoggerInterface;

/**
 * Class SplitOrderManagement
 * @package Netsteps\MarketplaceSales\Model\Order
 */
class SplitOrderManagement implements SplitOrderManagementInterface
{
    use OrderDataManagementTrait;
    use OrderItemDataManagementTrait;

    /**
     * Keep track of processed carts
     * @var array
     */
    private array $processedCarts = [];

    /**
     * @var \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface[]
     */
    private array $_errors = [];

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_orderRelationRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var OrderInfoBuyRequestGetter
     */
    private OrderInfoBuyRequestGetter $_orderInfoBuyRequest;

    /**
     * @var OrderRepository
     */
    private OrderRepository $_orderRepository;

    /**
     * @var CartRepository
     */
    private CartRepository $_cartRepository;

    /**
     * @var ProductRepository
     */
    private ProductRepository $_productRepository;

    /**
     * @var ShippingProcessor
     */
    private ShippingProcessor $_shippingProcessor;

    /**
     * @var PaymentProcessorInterface
     */
    private PaymentProcessor $_paymentProcessor;

    /**
     * @var CartManagement
     */
    private CartManagement $_cartManagement;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @var InvoiceProcessor
     */
    private InvoiceProcessor $_invoiceProcessor;

    /**
     * @var ErrorManager
     */
    private ErrorManager $_errorManager;

    /**
     * @var AppEmulation
     */
    private AppEmulation $_appEmulation;

    /**
     * @var StockItemRepository
     */
    private StockItemRepository $_stockItemRepository;

    /**
     * @var ValidationRegistry
     */
    private ValidationRegistry $_inventoryValidationRegistry;

    /**
     * @var SplitQuoteManagement
     */
    private SplitQuoteManagement $_splitQuoteManagement;

    /**
     * @param OrderRelationRepository $orderRelationRepository
     * @param OrderInfoBuyRequestGetter $orderInfoBuyRequestGetter
     * @param OrderRepository $orderRepository
     * @param ProductRepository $productRepository
     * @param CartRepository $cartRepository
     * @param ShippingProcessorInterface $shippingProcessor
     * @param PaymentProcessorInterface $paymentProcessor
     * @param CartManagement $cartManagement
     * @param EventManager $eventManager
     * @param ErrorManager $errorManager
     * @param InvoiceProcessorInterface $invoiceProcessor
     * @param AppEmulation $appEmulation
     * @param StockItemRepository $stockItemRepository
     * @param LoggerPool $loggerPool
     * @param ValidationRegistry $validationRegistry
     * @param SplitQuoteManagementInterface $splitQuoteManagement
     */
    public function __construct(
        OrderRelationRepository   $orderRelationRepository,
        OrderInfoBuyRequestGetter $orderInfoBuyRequestGetter,
        OrderRepository           $orderRepository,
        ProductRepository         $productRepository,
        CartRepository            $cartRepository,
        ShippingProcessor         $shippingProcessor,
        PaymentProcessor          $paymentProcessor,
        CartManagement            $cartManagement,
        EventManager              $eventManager,
        ErrorManager              $errorManager,
        InvoiceProcessor          $invoiceProcessor,
        AppEmulation              $appEmulation,
        StockItemRepository       $stockItemRepository,
        LoggerPool                $loggerPool,
        ValidationRegistry        $validationRegistry,
        SplitQuoteManagement      $splitQuoteManagement
    )
    {
        $this->_orderRelationRepository = $orderRelationRepository;
        $this->_orderInfoBuyRequest = $orderInfoBuyRequestGetter;
        $this->_orderRepository = $orderRepository;
        $this->_productRepository = $productRepository;
        $this->_cartRepository = $cartRepository;
        $this->_shippingProcessor = $shippingProcessor;
        $this->_paymentProcessor = $paymentProcessor;
        $this->_cartManagement = $cartManagement;
        $this->_eventManager = $eventManager;
        $this->_errorManager = $errorManager;
        $this->_invoiceProcessor = $invoiceProcessor;
        $this->_appEmulation = $appEmulation;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_inventoryValidationRegistry = $validationRegistry;
        $this->_splitQuoteManagement = $splitQuoteManagement;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @inheritDoc
     */
    public function splitFull(): array
    {
        $splitMap = [];
        $mainOrdersRelations = $this->_orderRelationRepository->getMainOrders(false);

        foreach ($mainOrdersRelations as $mainOrderRelation) {
            try {
                $order = $this->_orderRepository->get($mainOrderRelation->getMagentoOrderId());
                $connectedOrders = $this->processOrder($order, $mainOrderRelation->getProcessedSellerIds());
                $this->resolveProcess($mainOrderRelation, $order, $connectedOrders, $splitMap);
            } catch (\Exception $e) {
                $this->_logger->error(__('Error on split order: %1', $e->getMessage()));
            }
        }

        return $splitMap;
    }

    /**
     * @inheritDoc
     */
    public function splitSingle(int $orderId): array
    {
        $splitMap = [];
        $this->_errors = [];

        try {
            $order = $this->_orderRepository->get($orderId);

            if (!$this->canProcessOrder($order)) {
                $this->_logger->warning(
                    __(
                        'Order %1 cannot be processed because status %1 is invalid for processing',
                        [$order->getIncrementId(), $order->getStatus()]
                    )
                );
                return $splitMap;
            }

            $relation = $this->getOrderRelation($order);

            if ($relation->getIsProcessed()) {
                $childOrders = $this->_orderRelationRepository->getChildrenOrders($order);
                return array_map(function ($childOrder) {
                    return $childOrder->getEntityId();
                }, $childOrders);
            }

            $connectedOrders = $this->processOrder($order);
            $this->resolveProcess($relation, $order, $connectedOrders, $splitMap);
        } catch (\Exception $e) {
            $this->_logger->error(__('Error on split order with id %1. Reason: ', [$orderId, $e->getMessage()]));
        }

        return $splitMap;
    }

    /**
     * Process an order and create sub-orders for each seller
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $ignoredSellerIds
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function processOrder(\Magento\Sales\Api\Data\OrderInterface $order, array $ignoredSellerIds = []): array
    {
        $newOrders = [];
        $storeId = (int)$order->getStoreId();

        /** @var  $originalQuote  \Magento\Quote\Model\Quote */
        $originalQuote = $this->_cartRepository->get($order->getQuoteId());
        $shippingAddress = $originalQuote->getShippingAddress();
        $billingAddress = $originalQuote->getBillingAddress();
        $paymentMethod = $originalQuote->getPayment()->getMethod();

        /** Start frontend emulation for specific store view */
        $this->_appEmulation->startEnvironmentEmulation(
            $order->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND,
            true
        );

        $stockItemsToRevert = [];

        foreach ($this->getOrderItemsGroupedBySeller($order, $ignoredSellerIds) as $sellerId => $orderItems) {
            $stockItem = null;

            try {
                $canDeleteCart = true;
                $cart = $this->_createEmptyCartFromOrder($order);

                /** Check if cart id is all-ready processed */
                $this->checkProcessedCart($cart->getId(), $sellerId, $order->getIncrementId());

//                $this->_splitQuoteManagement->prepareQuote($cart);

                $cart->setReservedOrderId($order->getIncrementId() . '-s' . $sellerId);
                $cart->setStoreId($storeId);
                $cart->setIsSuperMode(true);
                $this->_inventoryValidationRegistry->registerIgnore((int)$cart->getEntityId());

                /** @var  $orderItem \Magento\Sales\Model\Order\Item */
                foreach ($orderItems as $orderItem) {
                    /** @var  $product \Magento\Catalog\Model\Product */
                    $product = clone $this->_productRepository->getById($orderItem->getProductId(), false, $storeId);
                    $product->setData(SellerManagementInterface::IGNORE_STOCK_CHECK, true);

                    $buyRequest = $this->createInfoBuyRequest($orderItem, $order);

                    /** Dispatch before add item to split cart */
                    $this->_eventManager->dispatch(
                        'marketplace_before_add_to_cart_split',
                        ['quote' => $cart, 'product' => $product, 'buy_request' => $buyRequest]
                    );

                    $stockItem = $this->_stockItemRepository->get(
                        $orderItem->getSku(),
                        $buyRequest[SellerManagementInterface::PARENT_ITEM_SOURCE_CODE]
                    );

                    /** Update stock before add to cart, so can pass all magento validations */
                    if ($stockItem) {
                        $productId = $this->getNormalizedItem($orderItem, $order)->getProductId();
                        $qty = $orderItem->getQtyOrdered();
                        $stockItem->setAmountModified($qty);
                        $stockItem->setModifiedProductId($productId);

                        $this->_stockItemRepository->increaseStock(
                            (int)$stockItem->getSourceItemId(),
                            $productId,
                            $stockItem->getSku(),
                            $qty
                        );

                        $product->setIsSalable(true);
                    }

                    $product->setData('ignore_seller_preparation', true);
                    $cart->addProduct($product, $buyRequest);
                }

                $cart->collectTotals();
                $this->_cartRepository->save($cart);
                $cart = $this->_cartRepository->get($cart->getId());

                if (!$cart->getItemsCount()) {
                    continue;
                }

                /** Prepare cart shipping address based on order's quote shipping address */
                $this->_shippingProcessor->prepare($cart, $shippingAddress);
                /** Prepare cart billing address and payment based on order's quote billing address and payment method */
                $needInvoice = $this->_paymentProcessor->preparePayment($cart, $billingAddress, $paymentMethod);

                /** Reload cart to have all necessary data  */
                $cart = $this->_cartRepository->get($cart->getId());

                /** Dispatch before submit quote event as magento core */
                $this->_eventManager->dispatch('checkout_submit_before', ['quote' => $cart, 'is_split' => true]);

                $cart->setData('is_split', true);

                $splitOrder = $this->_cartManagement->submit($cart);
                $splitOrder->setData('seller_id', $sellerId);

                $canDeleteCart = false;

                /** Dispatch after submit quote event as magento core */
                $this->_eventManager->dispatch('checkout_submit_all_after', ['order' => $splitOrder, 'quote' => $cart, 'is_split' => true]);

                $newOrders[] = $splitOrder;

                if ($needInvoice) {
                    $splitOrder->setData('marketplace_ignore_check', true);
                    $this->_invoiceProcessor->invoice($splitOrder);
                }
            } catch (\Exception $e) {
                if (isset($cart) && $canDeleteCart) {
                    $this->_cartRepository->delete($cart);
                }

                if ($stockItem) {
                    $stockItemsToRevert[] = $stockItem;
                }

                $this->addError((int)$order->getEntityId(), $sellerId, $e);
            }
        }

        /** Revert any stock items that are not successfully added to split order */
        $this->_stockItemRepository->revertItems($stockItemsToRevert);
        $this->_inventoryValidationRegistry->clear();

        /** Resolve/restore any registered quote data */
//        $this->_splitQuoteManagement->resolve();

        $this->_appEmulation->stopEnvironmentEmulation();
        return $newOrders;
    }

    /**
     * Create a new info buy request for add to cart
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param \Magento\Sales\Model\Order $order
     * @return DataObject
     */
    private function createInfoBuyRequest(
        \Magento\Sales\Model\Order\Item $orderItem,
        \Magento\Sales\Model\Order      $order,
    ): DataObject
    {
        $buyRequest = $this->_orderInfoBuyRequest->getInfoBuyRequest($orderItem);

        $sellerInfo = $this->exportSellerInfoData($this->getNormalizedItem($orderItem, $order));

        $sellerInfo = $sellerInfo['seller_info'] ?? null;

        if ($sellerInfo) {
            $buyRequest->setData('seller_info', $sellerInfo);
        }

        $buyRequest->unsetData(SellerManagementInterface::INFO_BUY_REQUEST_SELLER_ID)
            ->setData('custom_price', ($orderItem->getPriceInclTax() - (float)$orderItem->getDiscountAmount()))
            ->setData(SellerManagementInterface::PARENT_ITEM_ID, $orderItem->getItemId())
            ->setData(SellerManagementInterface::PARENT_ITEM_DISCOUNT_PERCENT, $orderItem->getDiscountPercent())
            ->setData(SellerManagementInterface::PARENT_ITEM_DISCOUNT_AMOUNT, $orderItem->getDiscountAmount())
            ->setData(SellerManagementInterface::PARENT_ITEM_ORIGINAL_PRICE, $orderItem->getPriceInclTax())
            ->setData(SellerManagementInterface::PARENT_ITEM_EAN, $this->getSellerEanFromItem($orderItem, $order))
            ->setData(SellerManagementInterface::PARENT_ITEM_SOURCE_CODE, $this->getItemSourceCode($orderItem, $order))
            ->setData(SellerManagementInterface::IGNORE_STOCK_CHECK, true);

        return $buyRequest;
    }

    /**
     * Get errors
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface[]
     */
    private function getErrors(?bool $clear = true): array
    {
        $errors = $this->_errors;

        if ($clear) {
            $this->_errors = [];
        }

        return $errors;
    }

    /**
     * Add error
     * @param int $orderId
     * @param int $sellerId
     * @param \Exception $e
     * @return void
     */
    private function addError(int $orderId, int $sellerId, \Exception $e): void
    {
        $error = new Error();
        $error
            ->setOrderId($orderId)
            ->setSellerId($sellerId)
            ->setErrorMessage($e->getMessage())
            ->setErrorTrace($e->getTraceAsString());

        $this->_errors[] = $error;
    }

    /**
     * Resolve the split process
     * @param OrderRelationInterface $mainOrderRelation
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $connectedOrders
     * @param array $splitMap
     * @return void
     */
    protected function resolveProcess(
        OrderRelationInterface                 $mainOrderRelation,
        \Magento\Sales\Api\Data\OrderInterface $order,
        array                                  $connectedOrders,
        array                                  &$splitMap
    ): void
    {
        $errors = $this->getErrors();
        $isProcessed = !empty($connectedOrders) && empty($errors);

        if (empty($connectedOrders) && empty($errors)) {
            return;
        }

        if (!empty($connectedOrders)) {
            $splitMap[$order->getEntityId()] = $this->_orderRelationRepository->linkOrders($order, $connectedOrders);

            $processedSellers = $mainOrderRelation->getProcessedSellerIds();
            foreach ($connectedOrders as $connectedOrder) {
                $processedSellers[] = (int)$connectedOrder->getData('seller_id');
            }
            $mainOrderRelation->setProcessedSellerIds($processedSellers);
        }

        if (!empty($errors)) {
            $this->_errorManager->saveMultiple($errors);
        }

        $mainOrderRelation->increaseTries();
        $mainOrderRelation->setIsProcessed($isProcessed);
        $this->_orderRelationRepository->save($mainOrderRelation);
    }

    /**
     * Check if the order can be processed
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     */
    private function canProcessOrder(\Magento\Sales\Api\Data\OrderInterface $order): bool
    {
        return !in_array($order->getStatus(), ['canceled', 'closed', 'pending_payment', 'fraud']);
    }

    /**
     * Get seller ean from item
     * @param \Magento\Sales\Model\Order\Item $item
     * @param \Magento\Sales\Model\Order $order
     * @return string|null
     */
    private function getSellerEanFromItem(\Magento\Sales\Model\Order\Item $item, \Magento\Sales\Model\Order $order): ?string
    {
        $normalizedItem = $this->getNormalizedItem($item, $order);
        return $this->getItemEan($normalizedItem);
    }

    /**
     * Get master item source code
     * @param \Magento\Sales\Model\Order\Item $item
     * @param \Magento\Sales\Model\Order $order
     * @return string|null
     */
    private function getItemSourceCode(\Magento\Sales\Model\Order\Item $item, \Magento\Sales\Model\Order $order): ?string
    {
        $normalizedItem = $this->getNormalizedItem($item, $order);
        return $this->exportSellerInfoData($normalizedItem)['seller_source'] ?? 'default';
    }

    /**
     * Get normalized item
     * @param \Magento\Sales\Model\Order\Item $item
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order\Item
     */
    private function getNormalizedItem(\Magento\Sales\Model\Order\Item $item, \Magento\Sales\Model\Order $order): \Magento\Sales\Model\Order\Item
    {
        if (!$item->getParentItemId()) {
            $itemCollection = $order->getItemsCollection();
            $item = $itemCollection->getItemByColumnValue('parent_item_id', $item->getItemId()) ?? $item;
        }
        return $item;
    }

    /**
     * Check for processed carts
     * @throws LocalizedException
     */
    private function checkProcessedCart(int $cartId, int $sellerId, string $orderId): void
    {
        if (in_array($cartId, $this->processedCarts)) {
            throw new LocalizedException(
                __(
                    'Cannot create partial order for seller %1 and order %2. Duplicate cart %3.',
                    [$sellerId, $orderId, $cartId]
                )
            );
        }

        $this->processedCarts[] = $cartId;
    }

    /**
     * Create an empty cart
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Quote\Model\Quote
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function _createEmptyCartFromOrder(\Magento\Sales\Model\Order $order): \Magento\Quote\Model\Quote {
        $cartId = $this->_cartManagement->createEmptyCart();

        /** @var \Magento\Quote\Model\Quote $cart */
        $cart = $this->_cartRepository->get($cartId);
        $cart->setCustomerEmail($order->getCustomerEmail());

        if (!$order->getCustomerIsGuest() || $order->getCustomerId()){
            $cart->setCustomerIsGuest(false);
            $cart->setCustomerId($order->getCustomerId());
            $cart->setCustomerGroupId($order->getCustomerGroupId());
            $cart->setCustomerFirstname($order->getCustomerFirstname());
            $cart->setCustomerLastname($order->getCustomerLastname());
            $cart->setCustomerGender($order->getCustomerGender());
            $cart->setCustomerDob($order->getCustomerDob());
        }

        return $cart;
    }
}
