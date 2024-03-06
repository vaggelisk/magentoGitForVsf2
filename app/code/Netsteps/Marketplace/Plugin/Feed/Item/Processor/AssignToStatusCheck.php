<?php
/**
 * AssignToStatusCheck
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Plugin\Feed\Item\Processor;

use Netsteps\Marketplace\Model\Feed\Action\SubAction\Status;
use Netsteps\Marketplace\Model\Feed\Action\SubActionManagerInterface;

/**
 * Class AssignToStatusCheck
 * @package Netsteps\Marketplace\Plugin\Feed\Item\Processor
 */
class AssignToStatusCheck
{
    /**
     * Add item to
     * @param \Netsteps\Marketplace\Model\Product\ItemProcessorInterface $processor
     * @param $result
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @param SubActionManagerInterface $subActionManager
     * @return void
     */
    public function afterProcess(
        \Netsteps\Marketplace\Model\Product\ItemProcessorInterface $processor,
        $result,
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        SubActionManagerInterface $subActionManager
    ): void {
        $subActionManager->addActionItem(Status::ACTION_CODE, $item);
    }
}
