<?php
/**
 * SimpleItem
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order;

use Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface;
use Magento\Framework\DataObject;

/**
 * Class SimpleItem
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order
 */
class SimpleItem extends DataObject implements SimpleItemInterface
{
    /**
     * @inheritDoc
     */
    public function getItemId(): int
    {
        return $this->_getData(self::ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function getQty(): int
    {
        return $this->_getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function setItemId(int $itemId): \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @inheritDoc
     */
    public function setQty(int $qty): \Netsteps\MarketplaceSales\Api\Data\SimpleItemInterface
    {
        return $this->setData(self::QTY, $qty);
    }
}
