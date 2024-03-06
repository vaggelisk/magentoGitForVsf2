<?php
/**
 * ActionPoolInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

/**
 * Interface ActionPoolInterface
 * @package Netsteps\Marketplace\Model\Feed
 */
interface ActionPoolInterface
{
    /**
     * Get action by code
     * @param string $code
     * @return \Netsteps\Marketplace\Model\Feed\ActionInterface|null
     */
    public function get(string $code): ?\Netsteps\Marketplace\Model\Feed\ActionInterface;

    /**
     * Get action by code or raise exception id it does not exist
     * @param string $code
     * @return \Netsteps\Marketplace\Model\Feed\ActionInterface
     */
    public function getOrException(string $code): \Netsteps\Marketplace\Model\Feed\ActionInterface;

    /**
     * Get all available actions
     * @return \Netsteps\Marketplace\Model\Feed\ActionInterface[]
     */
    public function getActions(): array;
}
