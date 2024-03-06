<?php
/**
 * SenderInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email;

/**
 * Interface SenderInterface
 * @package Netsteps\MarketplaceSales\Model\Order\Email
 */
interface SenderInterface
{
    /**
     * Send email
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function send(\Magento\Sales\Api\Data\OrderInterface $order): void;
}
