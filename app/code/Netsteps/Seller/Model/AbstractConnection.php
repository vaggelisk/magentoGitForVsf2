<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

abstract class AbstractConnection
{
    protected AdapterInterface $_connection;

    public function __construct(
        ResourceConnection $_connection
    )
    {
        $this->_connection = $_connection->getConnection();
    }
}
