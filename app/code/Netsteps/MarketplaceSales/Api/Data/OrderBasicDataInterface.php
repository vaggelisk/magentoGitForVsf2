<?php
/**
 * OrderBasicDataInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface OrderBasicDataInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface OrderBasicDataInterface
{
    const ORDER_ID = 'order_id';
    const INCREMENT_ID = 'increment_id';
    const STATUS = 'status';
    const STATE = 'state';
    const SHIPPING_METHOD = 'shipping_method';
    const PAYMENT_METHOD = 'payment_method';
    const ITEM_COUNT = 'item_count';
    const GRAND_TOTAL = 'grand_total';
    const CREATION_DATE = 'creation_date';

     /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * @return string
     */
    public function getIncrementId(): string;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @return string
     */
    public function getShippingMethod(): string;

    /**
     * @return string
     */
    public function getPaymentMethod(): string;

    /**
     * @return int
     */
    public function getItemCount(): int;

    /**
     * @return float
     */
    public function getGrandTotal(): float;

    /**
     * @return string
     */
    public function getCreationDate(): string;

    /**
     * Populate from order
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderBasicDataInterface
     */
    public function populateFromOrder(\Magento\Sales\Api\Data\OrderInterface $order): \Netsteps\MarketplaceSales\Api\Data\OrderBasicDataInterface;
}
