<?php
/**
 * ShipmentProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\MarketplaceSales\Model\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Magento\Sales\Api\ShipOrderInterface as ShipOrder;
use Magento\Sales\Api\Data\OrderInterfaceFactory as OrderFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface;
use Netsteps\MarketplaceSales\Exception\Order\ValidationException;
use Netsteps\MarketplaceSales\Traits\OrderDataManagementTrait;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Psr\Log\LoggerInterface;

/**
 * Class ShipmentProcessor
 * @package Netsteps\MarketplaceSales\Model\Order
 */
class ShipmentProcessor implements ShipmentProcessorInterface
{
    use OrderDataManagementTrait;

    /**
     * @var OrderFactory
     */
    private OrderFactory $_orderFactory;

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_orderRelationRepository;

    /**
     * @var ShipOrder
     */
    private ShipOrder $_orderShip;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @var OrderConfig
     */
    private OrderConfig $_orderConfig;

    /**
     * @param OrderFactory $orderFactory
     * @param OrderRelationRepository $orderRelationRepository
     * @param ShipOrder $shipOrder
     * @param EventManager $eventManager
     * @param OrderConfig $orderConfig
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        OrderFactory $orderFactory,
        OrderRelationRepository $orderRelationRepository,
        ShipOrder $shipOrder,
        EventManager $eventManager,
        OrderConfig $orderConfig,
        LoggerPool $loggerPool
    )
    {
        $this->_orderFactory = $orderFactory;
        $this->_orderRelationRepository = $orderRelationRepository;
        $this->_orderShip = $shipOrder;
        $this->_eventManager = $eventManager;
        $this->_orderConfig = $orderConfig;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws ValidationException
     */
    public function execute(string $orderId, bool $notify = false, array $tracks = []): int
    {

        /** @var  $order \Magento\Sales\Model\Order */
        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($orderId);

        if (!$order->getId()){
            throw new NoSuchEntityException(
                __('Order %1 does not exist.', $orderId)
            );
        }

        if (!$order->canShip() || $order->getStatus() !== OrderStatusManagementInterface::STATUS_APPROVED) {
            throw new ValidationException(
                __('Can not create shipment for order %1.', $orderId)
            );
        }

        $relation = $this->getOrderRelation($order);

        if (!$relation){
            throw new ValidationException(
                __('Order %1 is not registered.', $orderId)
            );
        }

        /** Dispatch custom event before create shipment for order */
        $this->_eventManager->dispatch(
            'marketplace_sales_order_ship_before',
            ['order' => $order, 'notify' => $notify, 'tracks' => $tracks, 'relation' => $relation]
        );

        $result = $this->_orderShip->execute(
            (int)$order->getId(),
            [],
            $notify,
            false,
            null,
            $tracks
        );

        $order->load($order->getEntityId());
        $order->setState(Order::STATE_COMPLETE);
        $order->setStatus($this->_orderConfig->getStateDefaultStatus($order->getState()));
        $order->save();

        return $result;
    }
}
