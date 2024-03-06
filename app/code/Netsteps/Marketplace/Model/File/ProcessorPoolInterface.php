<?php
/**
 * ProcessorPoolInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\File;

/**
 * Interface ProcessorPoolInterface
 * @package Netsteps\Marketplace\Model\File
 */
interface ProcessorPoolInterface
{
    /**
     * Get processor by code
     * @param string $code
     * @return ProcessorInterface|null
     */
    public function get(string $code): ?ProcessorInterface;

    /**
     * Get processor and raise an exception if processor does not exist
     * @param string $code
     * @return ProcessorInterface
     */
    public function getOrException(string $code): ProcessorInterface;

    /**
     * Get all available processors
     * @return ProcessorInterface[]
     */
    public function getAllProcessors(): array;
}
