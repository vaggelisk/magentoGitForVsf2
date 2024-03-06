<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\ResourceModel\Seller\Relation;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{

    /**
     * @inheritDoc
     */
    public function execute($entity, $arguments = [])
    {
        $entity->setData('options', ['poutsa' => 1]);
        dd('im132');
        return $entity;
    }
}
