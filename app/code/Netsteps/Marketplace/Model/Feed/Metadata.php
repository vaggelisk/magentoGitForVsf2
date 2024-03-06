<?php
/**
 * Metadata
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

use Magento\Framework\DataObject;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface;

/**
 * Class Metadata
 * @package Netsteps\Marketplace\Model\Feed
 */
class Metadata extends DataObject implements FeedMetadataInterface
{
    /**
     * @inheritDoc
     */
    public function getAvailableStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_INVALID,
            self::STATUS_FAILED,
            self::STATUS_SUCCESS
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAcceptedFileTypes(): array
    {
        return [
            self::TYPE_CSV,
            self::TYPE_XML
        ];
    }

    /**
     * @inheritDoc
     */
    public function isAcceptedType(string $fileType): bool
    {
        return in_array($fileType, $this->getAcceptedFileTypes());
    }
}
