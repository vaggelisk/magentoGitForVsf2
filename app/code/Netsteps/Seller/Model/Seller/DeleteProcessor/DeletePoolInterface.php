<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\DeleteProcessor;

interface DeletePoolInterface
{
    /**
     * @param array $data
     * @return void
     */
    public function execute(array $data = []):void;
}
