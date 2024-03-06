<?php
/**
 * AfterProcessSimpleProduct
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Observer\Marketplace;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\Marketplace\Model\Data\MerchantItemData;
use Netsteps\Marketplace\Model\Feed\Action\SubAction\Stock;
use Netsteps\Marketplace\Model\Feed\Action\SubActionManagerInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;

/**
 * Class AfterProcessSimpleProduct
 * @package Netsteps\Marketplace\Observer\Marketplace
 */
class AfterProcessSimpleProduct implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /**
         * @var  $product \Magento\Catalog\Model\Product|null
         * @var  $item ItemInterface
         * @var  $subActionManager SubActionManagerInterface
         */
        $product = $observer->getEvent()->getProduct();
        $item = $observer->getEvent()->getItem();
        $subActionManager = $observer->getEvent()->getSubActionManager();

        if ($product && !in_array($product->getTypeId(), MerchantItemData::getAvailableProductTypesToIndex())) {
            return;
        }

        $subActionManager->addActionItem(Stock::ACTION_CODE, $item);
    }
}
