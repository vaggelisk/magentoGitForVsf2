<?php
/**
 * SetStatusToOrderBeforeSubmit
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Quote;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetStatusToOrderBeforeSubmit
 * @package Netsteps\MarketplaceSales\Observer\Quote
 */
class SetSplitFlagToOrderBeforeSubmit implements ObserverInterface
{

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var  $order \Magento\Sales\Model\Order */
        $order = $observer->getOrder();

        /** @var  $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getQuote();

        if ($quote->getData('is_split')) {
            $order->setData('is_split', true);
        }
    }
}
