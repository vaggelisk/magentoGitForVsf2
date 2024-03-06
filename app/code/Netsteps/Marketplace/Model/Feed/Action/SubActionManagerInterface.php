<?php
/**
 * SubActionManagerInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action;

use Magento\Framework\DataObject;
use Netsteps\Seller\Api\Data\SellerInterface;

/**
 * Interface SubActionManagerInterface
 * @package Netsteps\Marketplace\Model\Feed\Action
 */
interface SubActionManagerInterface
{
    /**
     * Get seller for management
     * @return SellerInterface
     */
    public function getSeller(): SellerInterface;

    /**
     * Add action item
     * @param string $action
     * @param DataObject $item
     * @return $this
     */
    public function addActionItem(string $action, DataObject $item): self;

    /**
     * Resolve all actions that are registered
     * @return void
     */
    public function resolve(): void;

    /**
     * Resolve by action code
     * @param string $action
     * @return void
     */
    public function resolveByAction(string $action): void;
}
