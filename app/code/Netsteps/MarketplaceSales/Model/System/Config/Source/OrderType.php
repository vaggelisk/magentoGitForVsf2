<?php
/**
 * OrderType
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class OrderType
 * @package Netsteps\MarketplaceSales\Model\System\Config\Source
 */
class OrderType implements OptionSourceInterface
{
    const ORDER_TYPE_MASTER = 'master';
    const ORDER_TYPE_SPLIT = 'split';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ORDER_TYPE_MASTER, 'label' => __('Master Order')],
            ['value' => self::ORDER_TYPE_SPLIT, 'label' => __('Split Order')]
        ];
    }

    /**
     * Get order type
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public static function getOrderType(\Magento\Sales\Model\Order $order): string {
        return $order->getData('is_split') ? self::ORDER_TYPE_SPLIT : self::ORDER_TYPE_MASTER;
    }
}
