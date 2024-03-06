<?php
/**
 * ModifierInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Select;

/**
 * Interface ModifierInterface
 * @package Netsteps\Marketplace\Model\Select
 */
interface ModifierInterface
{
    /**
     * Modify a select input
     * @param \Magento\Framework\DB\Select $select
     * @return void
     */
    public function modify(\Magento\Framework\DB\Select $select): void;
}
