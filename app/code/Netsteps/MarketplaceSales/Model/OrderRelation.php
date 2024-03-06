<?php
/**
 * OrderRelation
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model;

use Magento\Framework\Model\AbstractModel;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderRelation as ResourceModel;

/**
 * Class OrderRelation
 * @package Netsteps\MarketplaceSales\Model
 */
class OrderRelation extends AbstractModel implements OrderRelationInterface
{
    protected $_idFieldName = self::ID;

    protected $_eventObject = self::EVENT_OBJECT;

    protected $_eventPrefix = self::EVENT_PREFIX;

    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Initialize resource
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getRelationId(): ?int
    {
        return $this->getId() ? (int)$this->getId() : null;
    }

    /**
     * @inheritDoc
     */
    public function getMagentoOrderId(): int
    {
        return (int)$this->_getData(self::MAGENTO_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getIsMainOrder(): bool
    {
        return (bool)(int)$this->_getData(self::IS_MAIN_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function getIsProcessed(): bool
    {
        return (bool)(int)$this->_getData(self::IS_PROCESSED);
    }

    /**
     * @inheritDoc
     */
    public function getParentOrderId(): ?int
    {
        return $this->hasData(self::PARENT_ORDER_ID) && $this->_getData(self::PARENT_ORDER_ID)
            ? (int)$this->_getData(self::PARENT_ORDER_ID) : null;
    }

    /**
     * @inheritDoc
     */
    public function getSellerId(): ?int
    {
        return $this->hasData(self::SELLER_ID) && $this->_getData(self::SELLER_ID) ?
            (int)$this->_getData(self::SELLER_ID) : null;
    }

    /**
     * @inheritDoc
     */
    public function getNumberOfTries(): int
    {
        return (int)$this->_getData(self::NUM_OF_TRIES);
    }

    /**
     * @inheritDoc
     */
    public function getProcessedSellerIds(): array
    {
        $sellerIds = $this->_getData(self::PROCESSED_SELLER_IDS);
        return $sellerIds ? explode(',', $sellerIds) : [];
    }

    /**
     * @inheritDoc
     */
    public function getRelationCreatedAt(): string
    {
        return $this->_getData(self::RELATION_CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getRelationUpdatedAt(): string
    {
        return $this->_getData(self::RELATION_UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setMagentoOrderId(int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        return $this->setData(self::MAGENTO_ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function setIsMainOrder(bool $isMain): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        return $this->setData(self::IS_MAIN_ORDER, $isMain);
    }

    /**
     * @inheritDoc
     */
    public function setIsProcessed(bool $isProcessed): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        return $this->setData(self::IS_PROCESSED, $isProcessed);
    }

    /**
     * @inheritDoc
     */
    public function setParentOrderId(?int $orderId): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        return $this->setData(self::PARENT_ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId(?int $sellerId): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function setNumberOfTries(int $numOfTries): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        if ($numOfTries > 0) {
            $this->setData(self::NUM_OF_TRIES, $numOfTries);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setProcessedSellerIds(array $sellerIds = []): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        $sellerIds = array_unique($sellerIds);
        sort($sellerIds);

        if (empty($sellerIds)){
            $value = null;
        } else {
            $value = implode(',', $sellerIds);
        }

        return $this->setData(self::PROCESSED_SELLER_IDS, $value);
    }

    /**
     * @inheritDoc
     */
    public function increaseTries(): \Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface
    {
        $tries = $this->getNumberOfTries() + 1;
        return $this->setNumberOfTries($tries);
    }
}
