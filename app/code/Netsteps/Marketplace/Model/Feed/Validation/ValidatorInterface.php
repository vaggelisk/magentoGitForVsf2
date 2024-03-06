<?php
/**
 * ValidatorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Validation;

/**
 * Interface ValidatorInterface
 * @package Netsteps\Marketplace\Model\Feed\Validation
 */
interface ValidatorInterface
{
    /**
     * Validate schema and return an array with the errors
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
     * @return string[]
     */
    public function validateSchema(
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
    ): array;
}
