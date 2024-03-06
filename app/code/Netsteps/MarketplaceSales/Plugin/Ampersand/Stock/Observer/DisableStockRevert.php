<?php
/**
 * DisableStockRevert
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Ampersand\Stock\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Sales\Model\Order\Item as OrderItem;
use Netsteps\MarketplaceSales\Model\System\Config\GeneralConfigurationInterface as Config;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Model\System\Config\Source\OrderType;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class DisableStockRevert
 * @package Netsteps\MarketplaceSales\Plugin\Ampersand\Stock\Observer
 */
class DisableStockRevert
{
    /**
     * @var Config
     */
    private Config $_config;

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_orderRelationRepository;

    /**
     * @var Logger
     */
    private Logger $_logger;

    /**
     * @param Config $config
     * @param OrderRelationRepository $orderRelationRepository
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        Config $config,
        OrderRelationRepository $orderRelationRepository,
        LoggerPool $loggerPool
    )
    {
        $this->_config = $config;
        $this->_orderRelationRepository = $orderRelationRepository;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * Around plugin to ampersand observer that revert the stock to an order item.
     * We check the order item in what type of order belongs and we disable the observer or not
     * depends on the configuration of the "Disable Stock Revert Order Types" field.
     *
     * @param \Ampersand\DisableStockReservation\Observer\CancelOrderItemObserver $cancelOrderItemObserver
     * @param callable $proceed
     * @param EventObserver $observer
     * @return void
     */
    public function aroundExecute(
        \Ampersand\DisableStockReservation\Observer\CancelOrderItemObserver $cancelOrderItemObserver,
        callable $proceed,
        EventObserver $observer
    ): void {
        /** @var OrderItem $orderItem */
        $orderItem = $observer->getEvent()->getItem();

        if ($orderItem instanceof OrderItem) {
            $disabledOrderTypesForRevert = $this->_config->getDisabledOrderTypesForStockRevert();
            $orderType = $this->getOrderType($orderItem->getOrderId());
            if (in_array($orderType, $disabledOrderTypesForRevert)) {
                return;
            }
        }

        $proceed($observer);
    }

    /**
     * Get order type based on magento order id
     * @param int $orderId
     * @return string
     */
    private function getOrderType(int $orderId): string {
        try {
            $relation = $this->_orderRelationRepository->getRelationByOrderId($orderId);
        } catch (\Exception $e) {
            $this->_logger->error(
                __('Exception on class %1. Reason %2',[get_class($this), $e->getMessage()])
            );

            return OrderType::ORDER_TYPE_MASTER;
        }

        return $relation->getIsMainOrder() ? OrderType::ORDER_TYPE_MASTER : OrderType::ORDER_TYPE_SPLIT;
    }
}
