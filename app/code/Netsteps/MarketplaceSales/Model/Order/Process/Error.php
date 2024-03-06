<?php
/**
 * Error
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Process;

use Magento\Framework\DataObject;
use Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface;

/**
 * Class Error
 * @package Netsteps\MarketplaceSales\Model\Order\Process
 */
class Error extends DataObject implements OrderProcessErrorInterface
{
    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->_getData(self::MAGENTO_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId(): int
    {
        return (int)$this->_getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return (string)$this->_getData(self::ERROR_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function getErrorTrace(): ?string
    {
        return $this->_getData(self::ERROR_TRACE);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->_getData('created_at');
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return $this->_getData('updated_at');
    }

    /**
     * @inheritDoc
     */
    public function setOrderId(int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
    {
        return $this->setData(self::MAGENTO_ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId(int $sellerId): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function setErrorMessage(string $message): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
    {
        return $this->setData(self::ERROR_MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function setErrorTrace(?string $trace): \Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface
    {
        return $this->setData(self::ERROR_TRACE, $trace);
    }

    /**
     * @inheritDoc
     */
    public function getDataForInsertion(): array
    {
        $data = $this->toArray([
            self::MAGENTO_ORDER_ID,
            self::SELLER_ID,
            self::ERROR_MESSAGE,
            self::ERROR_TRACE
        ]);

        ksort($data);

        return $data;
    }

}
