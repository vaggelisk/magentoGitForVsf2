<?php
/**
 * ItemProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

use Netsteps\Marketplace\Model\Feed\Action\SubActionManagerInterface;

/**
 * Interface ItemProcessorInterface
 * @package Netsteps\Marketplace\Model\Product
 */
interface ItemProcessorInterface
{
    /**
     * Process feed item and create a product
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterface $item
     * @param SubActionManagerInterface $subActionManager
     * @return void
     */
    public function process(
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item,
        SubActionManagerInterface $subActionManager
    ): void;
}
