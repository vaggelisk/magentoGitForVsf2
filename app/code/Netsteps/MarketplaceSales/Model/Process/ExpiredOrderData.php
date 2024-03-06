<?php
/**
 * ExpiredOrderData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Process;

use Magento\Framework\DataObject;

/**
 * Class ExpiredOrderData
 * @package Netsteps\MarketplaceSales\Model\Process
 */
class ExpiredOrderData extends DataObject implements ExpiredOrderDataInterface
{

    /**
     * @inheritDoc
     */
    public function getSellerId(): int
    {
        return $this->_getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getOrderIds(): array
    {
        $ids = $this->_getData(self::ORDER_IDS);

        if (is_string($ids)){
            $ids = explode(',', $ids);
        }

        return  $ids ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setSellerId(int $sellerId): \Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function setOrderIds(array $orderIds): \Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface
    {
        return $this->setData(self::ORDER_IDS, $orderIds);
    }
}
