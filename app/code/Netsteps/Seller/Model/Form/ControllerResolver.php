<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Form;

class ControllerResolver
{

    const SELLER_EDIT_URL = 'sellers/seller/edit';

    const CONTROLLER_MAPPING = [
        'sellers_seller_edit' => self::SELLER_EDIT_URL
    ];

    /**
     * @param string $actionName
     * @return string
     */
    public function getEditPath(string $actionName):string
    {
        return self::CONTROLLER_MAPPING[$actionName] ?? '';
    }


}
