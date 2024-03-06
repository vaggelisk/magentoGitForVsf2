<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Product;

use Magento\Framework\App\ResourceConnection;
use Netsteps\Seller\Model\AbstractConnection;

class CleanDistributorData extends AbstractConnection
{

    private AttributeResolver $attributeResolver;

    /**
     * @param ResourceConnection $_connection
     * @param AttributeResolver $attributeResolver
     */
    public function __construct(
        ResourceConnection $_connection,
        AttributeResolver  $attributeResolver
    )
    {
        $this->attributeResolver = $attributeResolver;
        parent::__construct($_connection);
    }

    /**
     * @param int $sellerId
     * @return void
     */
    public function deleteAttributeData(int $sellerId): void
    {
        $attributeId = $this->attributeResolver->getDistributorAttributeId();
        if (!is_null($attributeId)) {
            $where = [];
            $where[] = $this->_connection->quoteInto('attribute_id = ?', $attributeId);
            $this->_connection->delete('catalog_product_entity_int', $where);
        }
    }

}
