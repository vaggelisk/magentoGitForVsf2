<?php
/**
 * ProcessorPool
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\File;

use Netsteps\Marketplace\Exception\FileProcessorException;

/**
 * Class ProcessorPool
 * @package Netsteps\Marketplace\Model\File
 */
class ProcessorPool implements ProcessorPoolInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private array $_processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->_processors = $processors;
    }

    /**
     * @inheritDoc
     */
    public function get(string $code): ?ProcessorInterface
    {
       return array_key_exists($code, $this->_processors) ? $this->_processors[$code] : null;
    }

    /**
     * @inheritDoc
     * @throws FileProcessorException
     */
    public function getOrException(string $code): ProcessorInterface
    {
        $processor = $this->get($code);

        if (is_null($processor)){
            throw new FileProcessorException(
                __('Processor for type "%1" does not exist.', $code)
            );
        }

        return $processor;
    }

    /**
     * @inheritDoc
     */
    public function getAllProcessors(): array
    {
        return $this->_processors;
    }
}
