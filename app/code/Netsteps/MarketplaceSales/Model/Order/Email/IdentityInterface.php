<?php
/**
 * IdentityInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email;

/**
 * Interface IdentityInterface
 * @package Netsteps\MarketplaceSales\Model\Order\Email
 */
interface IdentityInterface
{
    /**
     * Get if is enabled
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool;

    /**
     * Get email sender
     * @param int|null $storeId
     * @return string
     */
    public function getSender(?int $storeId = null): string;

    /**
     * Get template
     * @param int|null $storeId
     * @return string
     */
    public function getTemplate(?int $storeId = null): string;

    /**
     * Get allowed seller ids for this action
     * @param int|null $storeId
     * @return int[]
     */
    public function getAllowedSellers(?int $storeId = null): array;
}
