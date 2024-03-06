<?php
/**
 * ValidateOrderAccess
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Marketplace\Webapi;

/**
 * Class ValidateOrderAccess
 * @package Netsteps\MarketplaceSales\Plugin\Marketplace\Webapi
 */
class ValidateOrderAccess extends AbstractPlugin
{
    /**
     * Before plugin on create order data for REST area to restrict order data view per
     * only for order's seller integrated token.
     *
     * @param \Netsteps\MarketplaceSales\Model\Marketplace\OrderManagement $orderManagement
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     * @throws \Netsteps\MarketplaceSales\Exception\Order\ValidationException
     */
    public function beforeCreateOrderData(
        \Netsteps\MarketplaceSales\Model\Marketplace\OrderManagement $orderManagement,
        \Magento\Sales\Api\Data\OrderInterface $order
    ): void {
        $this->_isAllowedOrder(
            $order,
            __('You are not authorized for this order.')
        );
    }

    /**
     * Before plugin on refund order data for REST area to restrict order data view per
     * only for order's seller integrated token.
     *
     * @param \Netsteps\MarketplaceSales\Model\Marketplace\OrderManagement $orderManagement
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface[] $items
     * @return void
     * @throws \Netsteps\MarketplaceSales\Exception\Order\ValidationException
     */
    public function beforeRefund(
        \Netsteps\MarketplaceSales\Model\Marketplace\OrderManagement $orderManagement,
        \Magento\Sales\Api\Data\OrderInterface $order,
        array $items
    ): void {
        $this->_isAllowedOrder(
            $order,
            __('You are not authorized to refund this order.')
        );
    }
}
