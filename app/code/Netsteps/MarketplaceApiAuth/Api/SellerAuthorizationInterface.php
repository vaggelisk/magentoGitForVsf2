<?php
/**
 * SellerAuthorizationInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceApiAuth
 */

namespace Netsteps\MarketplaceApiAuth\Api;

use Magento\Framework\Phrase;

/**
 * Interface SellerAuthorizationInterface
 * @package Netsteps\MarketplaceApiAuth\Api
 */
interface SellerAuthorizationInterface
{
    const DEFAULT_MESSAGE = 'Seller %1 does not have access for this action.';

    /**
     * Check if seller id given is allowed to make any action in rest API area
     * @param int $sellerId
     * @param bool $raiseException
     * @param Phrase|null $message
     * @return bool
     */
    public function isAllowed(int $sellerId, bool $raiseException = false, ?Phrase $message = null): bool;
}
