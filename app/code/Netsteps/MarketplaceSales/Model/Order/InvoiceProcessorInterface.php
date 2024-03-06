<?php
/**
 * InvoiceProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

/**
 * Interface InvoiceProcessorInterface
 * @package Netsteps\MarketplaceSales\Model\Order
 */
interface InvoiceProcessorInterface
{
    /**
     * Create invoice for an order
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    public function invoice(\Magento\Sales\Api\Data\OrderInterface $order): int;

    /**
     * Create an invoice for a fiven order id
     * @param int $orderId
     * @return int
     */
    public function invoiceByOrderId(int $orderId): int;
}
