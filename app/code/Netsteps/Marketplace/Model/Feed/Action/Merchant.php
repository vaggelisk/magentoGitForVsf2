<?php
/**
 * Merchant
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action;

use Magento\Framework\App\ObjectManager;
use Netsteps\Marketplace\Model\Processor\Product\MerchantProcessorInterface as MerchantProcessor;

/**
 * Class Merchant
 * @package Netsteps\Marketplace\Model\Feed\Action
 */
class Merchant extends AbstractAction
{
    /**
     * @var MerchantProcessor
     */
    private MerchantProcessor $_merchantProcessor;

    /**
     * @inheritDoc
     */
    public function execute(
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
    ): void
    {
        $items = $processor->processItemData($feed->getFeedData(), 'feed/products/product');
        $result = $this->_merchantProcessor->processItems($items, $feed->getSellerId());

        /** Dispatch event after process merchant feed (XML/CSV) */
        $this->_eventManager->dispatch(
            'after_merchant_process',
            [
                'processor' => $processor,
                'feed' => $feed,
                'result' => $result,
                'items' => $items
            ]
        );
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_merchantProcessor = ObjectManager::getInstance()->get(MerchantProcessor::class);
    }
}
