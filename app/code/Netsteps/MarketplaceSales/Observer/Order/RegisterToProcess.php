<?php
/**
 * RegisterToProcess
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Psr\Log\LoggerInterface;

/**
 * Class RegisterToProcess
 * @package Netsteps\MarketplaceSales\Observer\Order
 */
class RegisterToProcess implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_orderRelationRepository;

    /**
     * @param OrderRelationRepository $orderRelationRepository
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        OrderRelationRepository $orderRelationRepository,
        LoggerPool $loggerPool
    )
    {
        $this->_orderRelationRepository = $orderRelationRepository;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if ($observer->getIsSplit()) {
            return;
        }

        /** @var  $order \Magento\Sales\Model\Order */
        $order = $observer->getOrder();

        if (!$order || !$order->getEntityId()) {
            return;
        }

        $this->_orderRelationRepository->registerOrder($order);
    }
}
