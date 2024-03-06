<?php
/**
 * ValidatorInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Validation\Csv;

/**
 * Interface ValidatorInterface
 * @package Netsteps\Marketplace\Model\Feed\Validation\Csv
 */
interface ValidatorInterface extends \Netsteps\Marketplace\Model\Feed\Validation\ValidatorInterface
{
    /**
     * Validator row and return the errors
     * @param array $row
     * @return array
     */
    public function validateRow(array $row): array;

    /**
     * Get invalid rows after validation schema
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @return array
     */
    public function getInvalidRows(\Netsteps\Marketplace\Api\Data\FeedInterface $feed): array;
}
