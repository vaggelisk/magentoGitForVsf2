<?php
/**
 * ModifierInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Order\Email\Variables;

/**
 * Interface ModifierInterface
 * @package Netsteps\MarketplaceSales\Observer\Order\Email\Variables
 */
interface ModifierInterface
{
    /**
     * Modifier that modify transaction object of email
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Framework\DataObject $transactionObject
     * @return void
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Framework\DataObject $transactionObject
    ): void;
}
