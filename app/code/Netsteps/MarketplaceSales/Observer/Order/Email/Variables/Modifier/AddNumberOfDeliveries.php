<?php
/**
 * AddNumberOfDeliveries
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Order\Email\Variables\Modifier;

use Netsteps\MarketplaceSales\Observer\Order\Email\Variables\ModifierInterface;
use Netsteps\MarketplaceSales\Traits\OrderItemDataManagementTrait;

/**
 * Class AddNumberOfDeliveries
 * @package Netsteps\MarketplaceSales\Observer\Order\Email\Variables\Modifier
 */
class AddNumberOfDeliveries implements ModifierInterface
{
    use OrderItemDataManagementTrait;

    /**
     * @inheritDoc
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Framework\DataObject $transactionObject
    ): void
    {
        $numOfDeliveries = $order->getExtensionAttributes()->getNumberOfDeliveries() ?? $this->getNumberOfDeliveries($order);

        if (!$numOfDeliveries){
            $numOfDeliveries = 1;
        }

        $transactionObject->setData('numOfDeliveries', $numOfDeliveries);
    }
}
