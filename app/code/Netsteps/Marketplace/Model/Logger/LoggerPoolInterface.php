<?php
/**
 * LoggerPoolInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Logger;

/**
 * Interface LoggerPoolInterface
 * @package Netsteps\Marketplace\Model\Logger
 */
interface LoggerPoolInterface
{
    /**
     * Get a logger
     * @param string|null $type
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(?string $type = null): \Psr\Log\LoggerInterface;

    /**
     * Get magento default logger
     * @return \Psr\Log\LoggerInterface
     */
    public function getDefaultLogger(): \Psr\Log\LoggerInterface;
}
