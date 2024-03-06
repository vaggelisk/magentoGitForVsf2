<?php
/**
 * Handler
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Logger
 */

namespace Netsteps\Logger\Model;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Monolog\Logger;

class Handler extends Base
{
    /**
     * @var Timezone
     */
    protected $_timeZone;

    protected $loggerType = Logger::INFO;

    /**
     * Handler constructor.
     * @param DriverInterface $filesystem
     * @param Timezone $timezone
     * @param null $filePath
     * @param string $fileName
     * @param bool $dateSuffix
     * @throws \Exception
     */
    public function __construct(
        DriverInterface $filesystem,
        Timezone $timezone,
        $filePath = null,
        $fileName = '/var/log/netsteps',
        $dateSuffix = false
    )
    {
        $this->_timeZone = $timezone;

        if ($dateSuffix){
            $date = $this->_timeZone->date();
            $fileName = sprintf('%s_%s.log', $fileName, $date->format('Y_m_d'));
        } else {
            $fileName = $fileName . '.log';
        }

        parent::__construct($filesystem, $filePath, $fileName);
    }
}
