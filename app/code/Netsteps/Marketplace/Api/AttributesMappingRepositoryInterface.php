<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2023 Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api;

interface AttributesMappingRepositoryInterface
{

    /**
     * @param string $attributeCode
     * @return mixed[]
     */
    public function get(string $attributeCode): array;

}
