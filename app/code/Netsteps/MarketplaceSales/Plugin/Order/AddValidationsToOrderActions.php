<?php
/**
 * AfterValidations
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Order;

use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Traits\OrderDataManagementTrait;
use Psr\Log\LoggerInterface;

/**
 * Class AfterValidations
 * @package Netsteps\MarketplaceSales\Plugin\Order
 */
class AddValidationsToOrderActions
{
    use OrderDataManagementTrait;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param LoggerPool $loggerPool
     */
    public function __construct(LoggerPool $loggerPool){
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * After order's can  method check if order can process further
     * based on is main or not. If is main check children orders
     * @param \Magento\Sales\Model\Order $order
     * @param bool $result
     * @return bool
     */
    public function afterCanShip(
        \Magento\Sales\Model\Order $order,
        bool $result
    ) {
        return $result &&
            $this->canShipOrInvoice($order) &&
            !in_array(
                $order->getState(), [
                \Magento\Sales\Model\Order::STATE_COMPLETE,
                \Magento\Sales\Model\Order::STATE_CLOSED
            ]);
    }

    /**
     * After order's can invoice method check if order can process further
     * based on is main or not. If is main check children orders
     * @param \Magento\Sales\Model\Order $order
     * @param bool $result
     * @return bool
     */
    public function afterCanInvoice(
        \Magento\Sales\Model\Order $order,
        bool $result
    ) {
        return $result && $this->canShipOrInvoice($order);
    }

    /**
     * Override completely the canReorder method to disable re-ordering
     * @param \Magento\Sales\Model\Order $order
     * @param callable $proceed
     * @return bool
     */
    public function aroundCanReorder(
        \Magento\Sales\Model\Order $order,
        callable $proceed
    ): bool {
        return false;
    }

    /**
     * Override completely the canReorderIgnoreSalable method to disable re-ordering
     * @param \Magento\Sales\Model\Order $order
     * @param callable $proceed
     * @return bool
     */
    public function aroundCanReorderIgnoreSalable(
        \Magento\Sales\Model\Order $order,
        callable $proceed
    ): bool {
        return false;
    }

    /**
     * Override completely the canEdit method to disable order editing
     * @param \Magento\Sales\Model\Order $order
     * @param callable $proceed
     * @return bool
     */
    public function aroundCanEdit(
        \Magento\Sales\Model\Order $order,
        callable $proceed
    ): bool {
        return false;
    }

    /**
     * Override completely the canHold method to disable order holding
     * @param \Magento\Sales\Model\Order $order
     * @param callable $proceed
     * @return bool
     */
    public function aroundCanHold(
        \Magento\Sales\Model\Order $order,
        callable $proceed
    ): bool {
        return false;
    }
}
