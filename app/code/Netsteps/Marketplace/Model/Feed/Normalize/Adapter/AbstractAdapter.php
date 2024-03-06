<?php
/**
 * AbstractAdapter
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Normalize\Adapter;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Netsteps\Logger\Model\Logger;
use Netsteps\Marketplace\Model\Adminhtml\Context;
use Netsteps\Marketplace\Model\Feed\Normalize\AdapterInterface;
use Netsteps\Marketplace\Model\File\ProcessorPoolInterface as ProcessorPool;

/**
 * Class AbstractAdapter
 * @package Netsteps\Marketplace\Model\Feed\Normalize\Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var ArrayManager
     */
    protected ArrayManager $_arrayManager;

    /**
     * @var TimezoneInterface
     */
    protected TimezoneInterface $_timezone;

    /**
     * @var Logger
     */
    protected Logger $_logger;

    /**
     * @var ProcessorPool
     */
    protected $_processorPool;

    /**
     * @param Context $context
     * @param ProcessorPool $processorPool
     */
    public function __construct(Context $context, ProcessorPool $processorPool)
    {
        $this->_arrayManager = $context->getArrayManager();
        $this->_timezone = $context->getTimezone();
        $this->_logger = $context->getLogger();
        $this->_processorPool = $processorPool;
    }
}
