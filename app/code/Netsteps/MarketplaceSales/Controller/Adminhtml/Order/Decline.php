<?php
/**
 * Decline
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Controller\Adminhtml\Order;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Decline
 * @package Netsteps\MarketplaceSales\Controller\Adminhtml\Order
 */
class Decline extends AbstractOrderAction
{
    /**
     * @inheritDoc
     */
    protected function _execute(OrderInterface $order): void
    {
        $message = __('Order %1 declined from admin %2', [$order->getIncrementId(), $this->getCurrentUserFullName()]);
        $this->_orderStatusManagement->decline($order, $message);
        $this->messageManager->addSuccessMessage(__('Order #%1 declined', $order->getIncrementId()));
    }
}
