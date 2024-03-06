<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Config;

use Netsteps\Seller\Api\Data\SellerGroupInterface;

class SellerGroupOptionsSource implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @return array
     */
    public function toOptionArray():array
    {
        return [
            ['value' => SellerGroupInterface::GROUP_DISTRIBUTOR,  'label' => __('Distributor')],
            ['value' => SellerGroupInterface::GROUP_MERCHANT,  'label' => __('Mercant')],
        ];
    }
}
