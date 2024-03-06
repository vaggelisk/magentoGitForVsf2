<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class FeedTypeOptionsSource implements OptionSourceInterface
{
    const TYPE_MASTER = 'master';
    const TYPE_LOCALE = 'locale';
    const TYPE_MERCHANT = 'merchant';
    const TYPE_IMAGES = 'images';
    const AVAILABLE_TYPES = [self::TYPE_MASTER, self::TYPE_LOCALE, self::TYPE_MERCHANT, self::TYPE_IMAGES];

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return  [
            [
                'value' => self::TYPE_MASTER,
                'label' => __('Master')
            ],
            [
                'value' => self::TYPE_LOCALE,
                'label' => __('Locale')
            ],
            [
                'value' => self::TYPE_MERCHANT,
                'label' => __('Merchant')
            ],
            [
                'value' => self::TYPE_IMAGES,
                'label' => __('Images')
            ]
        ];
    }


}
