<?php
/**
 * DisableCreditMemoForComplete
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Order\Email;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class DisableCreditMemoForComplete
 * @package Netsteps\MarketplaceSales\Plugin\Order\Email
 */
class DisableCreditMemoForComplete
{
    /**
     * Disable credit memo email if order was already complete
     *
     * Order in this state has already set to "closed". So we should check the
     * status history for status "complete", but the status complete does not keep in status history.
     * So the approach is to check if at least one item is shipped. Maybe this process is flawed but
     * fits for projects flow at the moment.
     *
     * @param Order\Email\Sender\CreditmemoSender $sender
     * @param callable $proceed
     * @param Creditmemo $creditmemo
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSend(
        \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $sender,
        callable $proceed,
        Creditmemo $creditmemo,
        bool $forceSyncMode = false
    ): bool {
        $order = $creditmemo->getOrder();
        $isComplete = false;

        /** @var  $statusHistory \Magento\Sales\Model\Order\Item */
        foreach ($order->getItems() as $item) {
            if ($item->getQtyShipped() > 0){
                $isComplete = true;
                break;
            }
        }

        if ($isComplete){
            return true;
        }

        return $proceed($creditmemo, $forceSyncMode);
    }
}
