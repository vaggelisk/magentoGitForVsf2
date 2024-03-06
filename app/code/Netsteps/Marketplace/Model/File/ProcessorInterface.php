<?php
/**
 * ProcessorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\File;

/**
 * Interface ProcessorInterface
 * @package Netsteps\Marketplace\Model\File
 */
interface ProcessorInterface
{
    /**
     * Process data from given file path and transform them in string representation
     * @param string $filePath
     * @return string
     */
    public function processFile(string $filePath): string;

    /**
     * Process string data from process file method in an array of items
     * @param string $rawData
     * @param string|null $path
     * @return \Netsteps\Marketplace\Model\Feed\ItemInterface[]
     */
    public function processItemData(string $rawData, ?string $path = null): array;

    /**
     * Process data and return them as raw array
     * @param string $rawData
     * @return array
     */
    public function processDataAsArray(string $rawData): array;
}
