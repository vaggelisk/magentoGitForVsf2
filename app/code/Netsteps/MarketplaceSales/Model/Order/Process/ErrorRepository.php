<?php
/**
 * ErrorRepository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Process;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Netsteps\MarketplaceSales\Api\OrderProcessErrorRepositoryInterface;
use Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterface as OrderError;
use Netsteps\MarketplaceSales\Api\Data\OrderProcessErrorInterfaceFactory as OrderErrorFactory;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
/**
 * Class ErrorRepository
 * @package Netsteps\MarketplaceSales\Model\Order\Process
 */
class ErrorRepository implements OrderProcessErrorRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @var OrderErrorFactory
     */
    private OrderErrorFactory $_errorFactory;

    /**
     * @param OrderErrorFactory $errorFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        OrderErrorFactory $errorFactory,
        ResourceConnection $resourceConnection
    )
    {
        $this->_errorFactory = $errorFactory;
        $this->_connection = $resourceConnection->getConnection();
    }

    /**
     * @inheritDoc
     */
    public function save(OrderError $error): void
    {
        $data = $error->getDataForInsertion();
        $this->_connection->insert($this->getTable(), $data);
    }

    /**
     * @inheritDoc
     */
    public function saveMultiple(array $errors): void
    {
        $inserts = [];

        foreach ($errors as $error) {
            $inserts[] = $error->getDataForInsertion();
        }

        if (!empty($inserts)){
            $this->_connection->insertMultiple($this->getTable(), $inserts);
        }
    }

    /**
     * @inheritDoc
     */
    public function getErrorsByOrderId(int $orderId, ?int $sellerId = null): array
    {
        $select = $this->_connection->select()
            ->from(['main_table' => $this->getTable()])
            ->where(OrderError::MAGENTO_ORDER_ID . ' = ?', $orderId);

        if ($sellerId) {
            $select->where(OrderError::SELLER_ID . ' = ?', $sellerId);
        }

        $select->order('created_at ASC');

        $results = [];
        $rawData = $this->_connection->fetchAll($select);

        foreach ($rawData as $data) {
            $error = new Error();
            $error->setData($data);
            $results[] = $error;
        }

        return $results;
    }

    /**
     * Get error log table name
     * @return string
     */
    protected function getTable(): string {
        return $this->_connection->getTableName(OrderError::TABLE);
    }
}
