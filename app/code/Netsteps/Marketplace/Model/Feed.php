<?php
/**
 * Feed
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Netsteps\Marketplace\Api\Data\FeedInterface;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface;
use Netsteps\Marketplace\Exception\InvalidValueException;
use Netsteps\Marketplace\Model\ResourceModel\Feed as ResourceModel;

/**
 * Class Feed
 * @package Netsteps\Marketplace\Model
 */
class Feed extends AbstractModel implements FeedInterface
{
    protected $_idFieldName = self::ID;

    protected $_eventPrefix = self::EVENT_PREFIX;

    protected $_eventObject = self::EVENT_OBJECT;

    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var FeedMetadataInterface
     */
    private FeedMetadataInterface $_metadata;

    /**
     * Initialize resource
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
        $this->_metadata = ObjectManager::getInstance()->get(FeedMetadataInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function getFeedId(): ?int
    {
        return $this->getId() ? (int)$this->getId() : null;
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
    public function getStatus(): string
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getFeedData(): string
    {
        return $this->_getData(self::FEED_DATA);
    }

    /**
     * @inheritDoc
     */
    public function getFeedType(): string
    {
        return $this->_getData(self::FEED_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getFileType(): string
    {
        return $this->_getData(self::FILE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalInfo(): ?string
    {
        return $this->_getData(self::ADDITIONAL_INFO);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId(int $sellerId): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     * @throws InvalidValueException
     */
    public function setStatus(string $status): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        $availableStatuses = $this->_metadata->getAvailableStatuses();

        if (!in_array($status, $availableStatuses)) {
            throw new InvalidValueException(
                __(
                    'Status %1 is invalid. Acceptable statuses are: %2',
                    [$status, implode(', ', $availableStatuses)]
                )
            );
        }

        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function setFeedData(string $dataEncoded): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        return $this->setData(self::FEED_DATA, $dataEncoded);
    }

    /**
     * @inheritDoc
     * @throws InvalidValueException
     */
    public function setFeedType(string $type): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        return $this->setData(self::FEED_TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalInfo(?string $info): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        return $this->setData(self::ADDITIONAL_INFO, $info);
    }

    /**
     * @inheritDoc
     * @throws InvalidValueException
     */
    public function setFileType(string $fileType): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        if (!$this->_metadata->isAcceptedType($fileType)) {
            throw new InvalidValueException(
                __(
                    'File type "%1" is invalid. Acceptable file types are: %2',
                    [$fileType, implode(', ', $this->_metadata->getAcceptedFileTypes())]
                )
            );
        }
        return $this->setData(self::FILE_TYPE, $fileType);
    }
}
