<?php
/**
 * Approve
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Controller\Adminhtml\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Approve
 * @package Netsteps\MarketplaceSales\Controller\Adminhtml\Order
 */
class Approve extends AbstractOrderAction
{

    /**
     * @inheritDoc
     */
    protected function _execute(OrderInterface $order): void
    {
        $message = __('Order %1 approved from admin %2', [$order->getIncrementId(), $this->getCurrentUserFullName()]);
        $this->_orderStatusManagement->approve($order, $message);
        $this->messageManager->addSuccessMessage(__('Order #%1 approved', $order->getIncrementId()));
    }
}
