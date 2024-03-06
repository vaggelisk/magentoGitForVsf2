<?php
/**
 * SellerWithoutEmpty
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\System\Config\Source;

/**
 * Class SellerWithoutEmpty
 * @package Netsteps\Marketplace\Model\System\Config\Source
 */
class SellerWithoutEmpty extends \Netsteps\Seller\Model\Config\SellersOptionsSource
{
    /**
     * Get a seller list without empty option
     * @return array
     */
    public function toOptionArray(): array
    {
        $data = parent::toOptionArray();
        array_shift($data);
        return $data;
    }
}
