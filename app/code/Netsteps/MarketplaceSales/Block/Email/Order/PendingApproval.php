<?php
/**
 * PendingApproval
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Block\Email\Order;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class PendingApproval
 * @package Netsteps\MarketplaceSales\Block\Email\Order
 */
class PendingApproval extends Template
{
    protected $_template = 'Netsteps_MarketplaceSales::order/email/pending/approval/items.phtml';

    /**
     * Get orders
     * @return OrderInterface[]
     */
    public function getOrders(): array {
        return $this->_getData('orders') ?? [];
    }

    /**
     * Set orders
     * @param OrderInterface[] $orders
     * @return $this
     */
    public function setOrders(array $orders): self {
        return $this->setData('orders', $orders);
    }
}
