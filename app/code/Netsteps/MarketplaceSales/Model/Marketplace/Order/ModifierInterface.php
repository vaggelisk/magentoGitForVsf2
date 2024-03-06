<?php
/**
 * ModifierInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order;

/**
 * Interface ModifierInterface
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order
 */
interface ModifierInterface
{
    /**
     * Modify order data based on magento order model
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Netsteps\MarketplaceSales\Api\Data\OrderInterface $orderData
     * @param \Magento\Sales\Api\Data\OrderInterface|null $parentOrder
     * @return void
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Netsteps\MarketplaceSales\Api\Data\OrderInterface $orderData,
        ?\Magento\Sales\Api\Data\OrderInterface $parentOrder = null
    ): void;
}
