<?php
/**
 * AfterSubmitSuccessQuote
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Api\OrderItemRegistryRepositoryInterface as Registry;
use Psr\Log\LoggerInterface;

/**
 * Class AfterSubmitSuccessQuote
 * @package Netsteps\MarketplaceSales\Observer\Order
 */
class AfterSubmitSuccessQuote implements ObserverInterface
{

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var Registry
     */
    private Registry $_registry;

    /**
     * @param Registry $registry
     * @param LoggerPool $loggerPool
     */
    public function __construct(Registry $registry, LoggerPool $loggerPool)
    {
        $this->_registry = $registry;
        $this->_logger = $loggerPool->getLogger('quote');
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var  $order \Magento\Sales\Model\Order */

        $order = $observer->getOrder();
        /** @var  $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getQuote();

        try {
            foreach ($order->getItems() as $orderItem) {
                $quoteItem = $quote->getItemById((int)$orderItem->getQuoteItemId());
                $this->_registry->register($quoteItem, $orderItem);
            }
        } catch (\Exception $e) {
            $this->_logger->critical(
                __('Error on register items for quote %1. Reason: %2', [$quote->getEntityId(), $e->getMessage()])
            );
        }
    }
}
