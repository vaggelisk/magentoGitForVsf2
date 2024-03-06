<?php
/**
 * AdditionalVariables
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Order\Email\Variables;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class AdditionalVariables
 * @package Netsteps\MarketplaceSales\Observer\Order\Email\Variables
 */
class AdditionalVariables implements ObserverInterface
{

    /**
     * @var ModifierInterface[]
     */
    private array $_modifiers;

    /**
     * @param ModifierInterface[] $modifiers
     */
    public function __construct(array $modifiers = [])
    {
        $this->_modifiers = $modifiers;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $transportObject = $observer->getData('transportObject');

        if (!$transportObject instanceof DataObject) {
            return;
        }

        $order = $transportObject->getOrder();

        if (!$order instanceof OrderInterface){
            return;
        }

        foreach ($this->_modifiers as $modifier){
            $modifier->execute($order, $transportObject);
        }
    }
}
