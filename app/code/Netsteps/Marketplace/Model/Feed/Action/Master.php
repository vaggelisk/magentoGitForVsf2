<?php
/**
 * Master
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action;

use Netsteps\Marketplace\Model\Feed\Item\StockMetadataInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;

/**
 * Class Master
 * @package Netsteps\Marketplace\Model\Feed\Action
 */
class Master extends AbstractAction
{
    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor): void
    {
        $errors = [];

        $items = $processor->processItemData($feed->getFeedData(), 'feed/products/product');
        $seller = $this->_sellerRepository->getById($feed->getSellerId());
        $source = $seller->getSourceCode();
        $subActionManager = $this->createSubActionManager($seller);
        $invalidSkus = $feed->getData('invalid_sku') ?? [];

        foreach ($items as $item) {
            if (!$item->hasData(ItemInterface::SKU) || in_array($item->getSku(), $invalidSkus)) {
                continue;
            }

            $item->setNsDistributor($feed->getSellerId());
            $item->setStockSourceCode($source);

            try {
                $this->processItem($item, $subActionManager);
            } catch (\Throwable $e) {
                $this->_logger->critical(__('Error on processing item "%1". %2', [$item->getSku(), $e->getMessage()]));
                $errors[] = __('Error on processing item "%1". %2', [$item->getSku(), $e->getMessage()]);
            }
        }

        if (!empty($errors)) {
            $this->handleProcessErrors($feed, $errors);
            $feed->setHasErrors(true);
        }

        $subActionManager->resolve();

        /** Dispatch event after process master feed (XML/CSV) */
        $this->_eventManager->dispatch(
            'after_master_process',
            [
                'processor' => $processor,
                'feed' => $feed,
                'items' => $items,
                'sub_action_manager' => $subActionManager
            ]
        );
    }
}
