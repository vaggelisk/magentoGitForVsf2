<?php
/**
 * Metadata
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model;

use Magento\Framework\DataObject;
use Netsteps\MarketplaceSales\Api\Data\MetadataInterface;

/**
 * Class Metadata
 * @package Netsteps\MarketplaceSales\Model
 */
class Metadata extends DataObject implements MetadataInterface
{
    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return $this->_getData(self::KEY_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?string
    {
        return $this->_getData(self::KEY_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code): \Netsteps\MarketplaceSales\Api\Data\MetadataInterface
    {
        return $this->setData(self::KEY_CODE, $code);
    }

    /**
     * @inheritDoc
     */
    public function setValue(?string $value): \Netsteps\MarketplaceSales\Api\Data\MetadataInterface
    {
        return $this->setData(self::KEY_VALUE, $value);
    }
}
