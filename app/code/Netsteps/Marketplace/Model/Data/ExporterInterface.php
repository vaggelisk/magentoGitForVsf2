<?php
/**
 * ExporterInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Data;

/**
 * Interface ExporterInterface
 * @package Netsteps\Marketplace\Model\Data
 */
interface ExporterInterface
{
    /**
     * Export data from data object
     * @param \Magento\Framework\DataObject $dataObject
     * @param array $excluded
     * @return array
     */
    public function export(\Magento\Framework\DataObject $dataObject, array $excluded = []): array;
}
