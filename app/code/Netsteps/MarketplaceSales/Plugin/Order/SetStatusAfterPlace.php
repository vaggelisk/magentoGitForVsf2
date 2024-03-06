<?php
/**
 * SetStatusAfterPlace
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Order;

use Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface;

/**
 * Class SetStatusAfterPlace
 * @package Netsteps\MarketplaceSales\Plugin\Order
 */
class SetStatusAfterPlace
{
    /**
     * Set pending approval status to order's that created from split process
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order $result
     * @return \Magento\Sales\Model\Order
     */
    public function afterPlace(
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order $result
    ): \Magento\Sales\Model\Order
    {
        if ($order->getData('is_split') || $result->getData('is_split')) {
            if (in_array($result->getState(), OrderStatusManagementInterface::ACCEPTED_STATES)) {
                $result->setStatus(OrderStatusManagementInterface::STATUS_PENDING_APPROVAL);
                $result->addCommentToStatusHistory('Move to pending approval status because generated from split process');
            }
        }

        return $result;
    }
}
