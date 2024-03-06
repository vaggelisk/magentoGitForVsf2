<?php
/**
 * OrderStatusManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api;

use Magento\Sales\Model\Order;

/**
 * Interface OrderStatusManagementInterface
 * @package Netsteps\MarketplaceSales\Api
 */
interface OrderStatusManagementInterface
{
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';
    const ACCEPTED_STATES = [Order::STATE_NEW, Order::STATE_PROCESSING];

    /**
     * Approve an order
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param string|null $message
     * @return string
     */
    public function approve(\Magento\Sales\Api\Data\OrderInterface $order, ?string $message = null): string;

    /**
     * Decline an order
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param string|null $message
     * @return string
     */
    public function decline(\Magento\Sales\Api\Data\OrderInterface $order, ?string $message = null): string;

    /**
     * Approve an order by id
     * @param int $orderId
     * @param string|null $message
     * @return string
     */
    public function approveById(int $orderId, ?string $message = null): string;

    /**
     * Decline an order by id
     * @param int $orderId
     * @param string|null $message
     * @return string
     */
    public function declineById(int $orderId, ?string $message = null): string;

    /**
     * Get an array of statuses that mark child orders as approved
     * @return string[]
     */
    public static function getChildrenApprovedStatuses(): array;

    /**
     * Accept order by increment id
     * @param string $orderId
     * @param string|null $message
     * @return string
     */
    public function approveByIncrementId(string $orderId, ?string $message = null): string;

    /**
     * Decline order by increment id
     * @param string $orderId
     * @param string|null $message
     * @return string
     */
    public function declineByIncrementId(string $orderId, ?string $message = null): string;

}
