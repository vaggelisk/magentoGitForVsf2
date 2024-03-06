<?php
/**
 * ValidateOrderStatusAccess
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Marketplace\Webapi;

use Magento\Sales\Api\Data\OrderInterface;
use Netsteps\MarketplaceSales\Exception\Order\ValidationException;

/**
 * Class ValidateOrderStatusAccess
 * @package Netsteps\MarketplaceSales\Plugin\Marketplace\Webapi
 */
class ValidateOrderStatusAccess extends AbstractPlugin
{
    /**
     * Validate before decline order for rest api area
     * @param \Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface $orderStatusManagement
     * @param OrderInterface $order
     * @param string|null $message
     * @return void
     * @throws ValidationException
     */
    public function beforeApprove(
        \Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface $orderStatusManagement,
        OrderInterface $order,
        ?string $message = null
    ): void
    {
        $errorMessage = __('You are not authorized to approve the order %1', $order->getIncrementId());
        $this->_isAllowedOrder($order, $errorMessage);
    }

    /**
     * Validate before decline order for rest api area
     * @param \Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface $orderStatusManagement
     * @param OrderInterface $order
     * @param string|null $message
     * @return void
     * @throws ValidationException
     */
    public function beforeDecline(
        \Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface $orderStatusManagement,
        OrderInterface $order,
        ?string $message = null
    ): void
    {
        $errorMessage = __('You are not authorized to decline the order %1', $order->getIncrementId());
        $this->_isAllowedOrder($order, $errorMessage);
    }
}
