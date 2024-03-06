<?php
/**
 * OrderManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Netsteps\MarketplaceSales\Api\OrderManagementInterface;
use Netsteps\MarketplaceSales\Model\Marketplace\Order\ModifierInterface;
use Netsteps\MarketplaceSales\Model\Marketplace\Order\ModifierInterface as Modifier;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder as CriteriaBuilder;
use Netsteps\MarketplaceSales\Api\Data\OrderInterface as OrderData;
use Netsteps\MarketplaceSales\Api\Data\OrderInterfaceFactory as OrderDataFactory;
use Netsteps\MarketplaceSales\Model\Order\Payment\MethodMapperInterface as PaymentMethodMapper;
use Netsteps\MarketplaceSales\Model\Order\Shipping\MethodMapperInterface as ShippingMethodMapper;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Magento\Sales\Api\RefundOrderInterface as CreditmemoManagement;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterface as CreditmemoItem;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterfaceFactory as CreditmemoItemFactory;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface as CreditmemoComment;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterfaceFactory as CreditmemoCommentFactory;

/**
 * Class OrderManagement
 * @package Netsteps\MarketplaceSales\Model\Marketplace
 */
class OrderManagement implements OrderManagementInterface
{
    const INVALID_REFUND_STATUSES = [
//        \Magento\Sales\Model\Order::STATE_COMPLETE,
        \Magento\Sales\Model\Order::STATE_CANCELED,
        \Magento\Sales\Model\Order::STATE_CLOSED,
        \Magento\Sales\Model\Order::STATE_HOLDED,
    ];

    /**
     * @var OrderRepository
     */
    private OrderRepository $_orderRepository;

    /**
     * @var OrderDataFactory
     */
    private OrderDataFactory $_orderDataFactory;

    /**
     * @var CriteriaBuilder
     */
    private CriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @var Modifier
     */
    private Modifier $_productModifier;

    /**
     * @var Modifier
     */
    private Modifier $_addressModifier;

    /**
     * @var ModifierInterface[]
     */
    private array $_modifiers;

    /**
     * @var PaymentMethodMapper
     */
    private PaymentMethodMapper $_paymentMapper;

    /**
     * @var ShippingMethodMapper
     */
    private ShippingMethodMapper $_shippingMapper;

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_orderRelationRepository;


    /**
     * @var CreditmemoManagement
     */
    private CreditmemoManagement $_creditmemoManagement;

    /**
     * @var CreditmemoItemFactory
     */
    private CreditmemoItemFactory $_creditmemoItemFactory;

    /**
     * @var CreditmemoCommentFactory
     */
    private CreditmemoCommentFactory $_creditmemoCommentFactory;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderDataFactory $orderDataFactory
     * @param CriteriaBuilder $criteriaBuilder
     * @param Modifier $productModifier
     * @param Modifier $addressModifier
     * @param PaymentMethodMapper $paymentMapper
     * @param ShippingMethodMapper $shippingMapper
     * @param OrderRelationRepository $orderRelationRepository
     * @param CreditmemoManagement $creditmemoManagement
     * @param CreditmemoItemFactory $creditmemoItemFactory
     * @param CreditmemoCommentFactory $creditmemoCommentFactory
     * @param array $modifiers
     */
    public function __construct(
        OrderRepository          $orderRepository,
        OrderDataFactory         $orderDataFactory,
        CriteriaBuilder          $criteriaBuilder,
        Modifier                 $productModifier,
        Modifier                 $addressModifier,
        PaymentMethodMapper      $paymentMapper,
        ShippingMethodMapper     $shippingMapper,
        OrderRelationRepository  $orderRelationRepository,
        CreditmemoManagement     $creditmemoManagement,
        CreditmemoItemFactory    $creditmemoItemFactory,
        CreditmemoCommentFactory $creditmemoCommentFactory,
        array                    $modifiers = []
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_orderDataFactory = $orderDataFactory;
        $this->_searchCriteriaBuilder = $criteriaBuilder;
        $this->_productModifier = $productModifier;
        $this->_addressModifier = $addressModifier;
        $this->_paymentMapper = $paymentMapper;
        $this->_shippingMapper = $shippingMapper;
        $this->_orderRelationRepository = $orderRelationRepository;
        $this->_creditmemoManagement = $creditmemoManagement;
        $this->_creditmemoItemFactory = $creditmemoItemFactory;
        $this->_creditmemoCommentFactory = $creditmemoCommentFactory;
        $this->_modifiers = $modifiers;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        $order = $this->_orderRepository->get($id);
        return $this->createOrderData($order);
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByIncrementId(string $id): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        $order = $this->getOrderByIncrementId($id);
        return $this->getById($order->getId());
    }

    /**
     * Create order data based on order
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     * @return OrderData
     * @throws \Exception
     */
    public function createOrderData(\Magento\Sales\Api\Data\OrderInterface $order): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        /** @var  $orderData OrderData */
        $orderData = $this->_orderDataFactory->create();
        $parentOrder = $this->_orderRelationRepository->getParentOrder($order);

        $orderData->setIncrementId($order->getIncrementId())
            ->setStatus($order->getStatus())
            ->setCreatedAt($order->getCreatedAt())
            ->setExpiresAt($this->_getExpirationDate($order))
            ->setPaymentMethod(
                $this->_paymentMapper->getOriginalMethod($order->getPayment()->getMethod())
            )
            ->setShippingMethod(
                $this->_shippingMapper->getMappedMethod(
                    $parentOrder ? $parentOrder->getShippingMethod() : $order->getShippingMethod()
                )
            )
            ->setCouponCode($parentOrder ? $parentOrder->getCouponCode() : $order->getCouponCode())
            ->setGrandTotal($order->getGrandTotal())
            ->setVatValue($order->getTaxAmount());

        $this->_productModifier->execute($order, $orderData, $parentOrder);
        $this->_addressModifier->execute($order, $orderData, $parentOrder);

        foreach ($this->_modifiers as $modifier) {
            $modifier->execute($order, $orderData, $parentOrder);
        }

        return $orderData;
    }

    /**
     * Get expiration date
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     * @throws \Exception
     */
    private function _getExpirationDate(\Magento\Sales\Api\Data\OrderInterface $order): string
    {
        $days = 1;
        $createdData = new \DateTime($order->getCreatedAt());
        $interval = new \DateInterval("P{$days}D");
        $createdData->add($interval);
        return $createdData->format('Y-m-d H:i:s');
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function refundById(string $orderId, array $items): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        $order = $this->getOrderByIncrementId($orderId);
        return $this->refund($order, $items);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function refund(\Magento\Sales\Api\Data\OrderInterface $order, array $items): \Netsteps\MarketplaceSales\Api\Data\OrderInterface
    {
        $this->_validateOrderAndItems($order, $items);

        /** @var  $creditMemo Creditmemo */
        $creditMemoItems = [];
        $itemData = [];
        foreach ($items as $item) {
            /** @var  $creditMemoItem CreditmemoItem */
            $creditMemoItem = $this->_creditmemoItemFactory->create();
            $creditMemoItem->setQty($item->getQty())
                ->setOrderItemId($item->getItemId());

            $creditMemoItems[] = $creditMemoItem;
            $itemData[] = $item->getData();
        }

        /** @var  $comment CreditmemoComment */
        $comment = $this->_creditmemoCommentFactory->create();
        $comment->setComment(
            __('Order refunded from API. \n Payload: %1', json_encode($itemData))
        );

        $this->_creditmemoManagement->execute(
            $order->getEntityId(),
            $creditMemoItems,
            false,
            false,
            $comment
        );

        return $this->getById($order->getEntityId());
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface[] $items
     * @return void
     * @throws LocalizedException
     */
    private function _validateOrderAndItems(\Magento\Sales\Api\Data\OrderInterface $order, array $items)
    {
        if (in_array($order->getState(), self::INVALID_REFUND_STATUSES)) {
            throw new LocalizedException(
                __('Can not refund the order. Invalid state %1', $order->getState())
            );
        }

        foreach ($items as $item) {
            $orderItem = $order->getItemById($item->getItemId());

            if (!$orderItem) {
                throw new LocalizedException(
                    __('Items %1 does not exist.', $item->getItemId())
                );
            }

            if ($item->getQty() < 1) {
                throw new LocalizedException(
                    __(
                        'Invalid quantity to refund for item %1. Need to be greater than 0 but %2 given.',
                        [$item->getItemId(), $item->getQty()]
                    )
                );
            }

            $availableQtyToRefund = $orderItem->getQtyInvoiced() - ($orderItem->getQtyRefunded() + $orderItem->getQtyCanceled());

            if ($item->getQty() > $availableQtyToRefund) {
                throw new LocalizedException(
                    __(
                        'Invalid quantity to refund for item %1. Available quantity to refund is %2 but %3 given.',
                        [$item->getItemId(), $availableQtyToRefund, $item->getQty()]
                    )
                );
            }
        }
    }

    /**
     * Get order by increment id
     * @param string $id
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getOrderByIncrementId(string $id): \Magento\Sales\Api\Data\OrderInterface
    {
        $criteria = $this->_searchCriteriaBuilder->addFilter(
            \Magento\Sales\Api\Data\OrderInterface::INCREMENT_ID, $id,
        )->create();

        $searchResult = $this->_orderRepository->getList($criteria);

        if ($searchResult->getTotalCount() === 0) {
            throw new NoSuchEntityException(
                __('Order with id %1 does not exists.', $id)
            );
        }

        if ($searchResult->getTotalCount() > 1) {
            throw new LocalizedException(
                __('Order with id %1 found more than once.', $id)
            );
        }

        return array_values($searchResult->getItems())[0];
    }
}
