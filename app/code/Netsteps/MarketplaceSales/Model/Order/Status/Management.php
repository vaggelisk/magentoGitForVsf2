<?php
/**
 * ManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Status;

use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\Order;
use Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Sales\Api\Data\OrderInterface as OrderInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory as OrderFactory;
use Netsteps\MarketplaceSales\Exception\Order\ValidationException;
use Netsteps\MarketplaceSales\Model\Order\Email\SenderInterface as EmailSender;
use Magento\Sales\Api\RefundOrderInterface as RefundOrderService;

/**
 * Interface ManagementInterface
 * @package Netsteps\MarketplaceSales\Model\Order\Status
 */
class Management implements OrderStatusManagementInterface
{
    /**
     * @var OrderRepository
     */
    private OrderRepository $_orderRepository;

    /**
     * @var RefundOrderService
     */
    private RefundOrderService $_orderRefundService;

    /**
     * @var OrderFactory
     */
    private OrderFactory $_orderFactory;

    /**
     * @var EmailSender[]
     */
    private array $_emailSenders;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private ManagerInterface $_eventManager;

    /**
     * @param OrderRepository $orderRepository
     * @param RefundOrderService $refundOrderService
     * @param OrderFactory $orderFactory
     * @param EmailSender[] $emailSenders
     */
    public function __construct(
        OrderRepository $orderRepository,
        RefundOrderService $refundOrderService,
        OrderFactory $orderFactory,
        ManagerInterface $eventManager,
        array $emailSenders = []
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_orderRefundService = $refundOrderService;
        $this->_orderFactory = $orderFactory;
        $this->_emailSenders = $emailSenders;
        $this->_eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function approve(\Magento\Sales\Api\Data\OrderInterface $order, ?string $message = null): string
    {
        if (!in_array($order->getState(), self::ACCEPTED_STATES)) {
            throw new ValidationException(
                __('Order\'s status is not accepted')
            );
        }

        if ($order->getStatus() !== self::STATUS_PENDING_APPROVAL){
            throw new ValidationException(
                __('Current order status "%1" is not valid to approve or decline.', $order->getStatus())
            );
        }

        $this->_eventManager->dispatch('marketplace_order_approve_before', ['order' => $order]);

        /** @var $order \Magento\Sales\Model\Order */
        $order->setStatus(self::STATUS_APPROVED)
            ->setState(Order::STATE_PROCESSING);

        if (!is_null($message) && trim($message) !== ''){
            $order->addCommentToStatusHistory(trim($message));
        }

        $this->_orderRepository->save($order);

        $this->_eventManager->dispatch('marketplace_order_approve_after', ['order' => $order]);

        $this->sendEmail(__FUNCTION__, $order);

        return $order->getStatus();
    }

    /**
     * @inheritDoc
     */
    public function approveById(int $orderId, ?string $message = null): string
    {
        $order = $this->_orderRepository->get($orderId);
        return $this->approve($order, $message);
    }

    /**
     * @inheritDoc
     */
    public static function getChildrenApprovedStatuses(): array
    {
        return ['approved', 'pending', 'processing', 'complete'];
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function decline(\Magento\Sales\Api\Data\OrderInterface $order, ?string $message = null): string
    {
        $this->_eventManager->dispatch('marketplace_order_decline_before', ['order' => $order]);
        /** @var $order \Magento\Sales\Model\Order */
        if ($order->canCancel()) {
            $order->cancel();
        } else if ($order->canCreditmemo()) {
            $this->_orderRefundService->execute($order->getEntityId());
        } else {
            throw new ValidationException(
               __('Can not decline order right now. Order is not available to cancel or refund')
            );
        }

        if (!$message){
            $message = __('Order #1 declined from seller', $order->getIncrementId());
        }

        $order = $this->_orderRepository->get($order->getEntityId());

        $order->setStatus(OrderStatusManagementInterface::STATUS_DECLINED);

        $order->addCommentToStatusHistory($message);

        $order = $this->_orderRepository->save($order);

        $this->_eventManager->dispatch('marketplace_order_decline_after', ['order' => $order]);

        $this->sendEmail(__FUNCTION__, $order);

        return $order->getStatus();
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function declineById(int $orderId, ?string $message = null): string
    {
        $order = $this->_orderRepository->get($orderId);
        return $this->decline($order, $message);
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function approveByIncrementId(string $orderId, ?string $message = null): string
    {
        /** @var  $order Order */
        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($orderId);
        return $this->approve($order, $message);
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function declineByIncrementId(string $orderId, ?string $message = null): string
    {
        /** @var  $order Order */
        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($orderId);
        return $this->decline($order, $message);
    }

    /**
     * Send email
     * @param string $sender
     * @param OrderInterface $order
     * @return void
     */
    protected function sendEmail(string $sender, \Magento\Sales\Api\Data\OrderInterface $order): void {
        $sender = $this->_emailSenders[$sender] ?? null;
        $sender?->send($order);
    }
}
