<?php
/**
 * LoggerPool
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Logger;

use Psr\Log\LoggerInterface as Logger;

/**
 * Class LoggerPool
 * @package Netsteps\Marketplace\Model\Logger
 */
class LoggerPool implements LoggerPoolInterface
{
    /**
     * @var Logger
     */
    private Logger $_defaultLogger;

    /**
     * @var Logger[]
     */
    private array $_loggers;

    /**
     * @param Logger $logger
     * @param array $loggers
     */
    public function __construct(Logger $logger, array $loggers = [])
    {
        $this->_defaultLogger = $logger;
        $this->_loggers = $loggers;
    }

    /**
     * @inheritDoc
     */
    public function getLogger(?string $type = null): \Psr\Log\LoggerInterface
    {
        return $type && array_key_exists($type, $this->_loggers) ? $this->_loggers[$type] : $this->getDefaultLogger();
    }

    /**
     * @inheritDoc
     */
    public function getDefaultLogger(): \Psr\Log\LoggerInterface
    {
        return $this->_defaultLogger;
    }
}
