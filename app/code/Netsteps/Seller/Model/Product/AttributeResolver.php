<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Product;

use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Model\AbstractConnection;

class AttributeResolver extends AbstractConnection
{

    /**
     * @return int|null
     */
    public function getDistributorAttributeId(): ?int
    {
        $result = null;
        $select = $this->_connection
            ->select()
            ->from(
                ['eav' => 'eav_attribute'],
                ['eav.attribute_id']
            )
            ->where('eav.attribute_code = ?', SellerInterface::DISTRIBUTOR_CODE);

        $attributeId = $this->_connection->fetchOne($select);
        if (!is_null($attributeId)) {
            $result = (int)$attributeId;
        }
        return $result;
    }

}
