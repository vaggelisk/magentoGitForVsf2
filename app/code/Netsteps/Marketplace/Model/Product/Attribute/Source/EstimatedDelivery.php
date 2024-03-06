<?php
/**
 * EstimatedDelivery
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\Table;

/**
 * Class EstimatedDelivery
 * @package Netsteps\Marketplace\Model\Product\Attribute\Source
 */
class EstimatedDelivery extends Table
{
    const DELIVERY_AVAILABLE = 1;
    const DELIVERY_FOUR_TO_SIX = 2;

    const DAY_MAP = [
        self::DELIVERY_AVAILABLE => '1-3',
        self::DELIVERY_FOUR_TO_SIX => '4-6'
    ];

    /**
     * Get all options
     * @param bool $withEmpty
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false): array
    {
        $options = $withEmpty ? [
            ['value' => '', 'label' => __('Choose Estimated Delivery')]
        ]: [];

        $options[] = ['value' => self::DELIVERY_AVAILABLE, 'label' => __('Available/Delivery in 1-3 days')];
        $options[] = ['value' => self::DELIVERY_FOUR_TO_SIX, 'label' => __('Delivery in 4-6 days')];

        return $options;
    }

    /**
     * Override get option text
     * @param $value
     * @return array|bool|mixed|string|null
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions(false, false) as $option){
            if ($value == $option['value']) {
                return $option['label'];
            }
        }

        return null;
    }
}
