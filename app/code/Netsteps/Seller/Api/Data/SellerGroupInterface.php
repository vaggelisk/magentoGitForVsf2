<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */
namespace Netsteps\Seller\Api\Data;

interface SellerGroupInterface
{
    const GROUP_MERCHANT = 'merchant';
    const GROUP_DISTRIBUTOR = 'distributor';
    const GROUP_DEFAULT = self::GROUP_MERCHANT;
    const AVAILABLE_GROUPS = [self::GROUP_MERCHANT, self::GROUP_DISTRIBUTOR];
}
