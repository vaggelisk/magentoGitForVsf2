<?php
/**
 * MetadataInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface MetadataInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface MetadataInterface
{
    const KEY_CODE = 'code';
    const KEY_VALUE = 'value';

    /**
     * Get meta code
     * @return string
     */
    public function getCode(): string;

    /**
     * Get meta value
     * @return string|null
     */
    public function getValue(): ?string;

    /**
     * Set meta code
     * @param string $code
     * @return \Netsteps\MarketplaceSales\Api\Data\MetadataInterface
     */
    public function setCode(string $code): \Netsteps\MarketplaceSales\Api\Data\MetadataInterface;

    /**
     * Get meta value
     * @param string|null $value
     * @return \Netsteps\MarketplaceSales\Api\Data\MetadataInterface
     */
    public function setValue(?string $value): \Netsteps\MarketplaceSales\Api\Data\MetadataInterface;
}
