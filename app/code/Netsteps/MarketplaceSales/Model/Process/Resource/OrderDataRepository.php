<?php
/**
 * OrderDataRepository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Process\Resource;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface as Connection;
use Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterface as ExpiredData;
use Netsteps\MarketplaceSales\Model\Process\ExpiredOrderDataInterfaceFactory as ExpiredDataFactory;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface as OrderRelation;

/**
 * Class OrderDataRepository
 * @package Netsteps\MarketplaceSales\Model\Process\Resource
 */
class OrderDataRepository implements OrderDataRepositoryInterface
{
    const DEFAULT_DAYS = 2;

    /**
     * @var Connection
     */
    private Connection $_connection;

    /**
     * @var ExpiredDataFactory
     */
    private ExpiredDataFactory $_dataFactory;

    /**
     * @var int
     */
    private int $days;

    /**
     * @param ResourceConnection $resourceConnection
     * @param ExpiredDataFactory $dataFactory
     * @param int $days
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ExpiredDataFactory $dataFactory,
        int $days = self::DEFAULT_DAYS
    )
    {
        $this->_connection = $resourceConnection->getConnection();
        $this->_dataFactory = $dataFactory;
        $this->days = abs($days);
    }

    /**
     * @inheritDoc
     */
    public function getExpiredPendingApproval(array $incrementIds = []): array
    {
        $pendingApproval = [];

        foreach ($this->fetchData($incrementIds) as $data) {
            $pendingApproval[] = $this->_dataFactory->create()->addData($data);
        }

        return $pendingApproval;
    }

    /**
     * Fetch raw MySQL data for orders that are pending to
     * @param array $incrementIds
     * @return array
     */
    private function fetchData(array $incrementIds = []): array {
        $fkOrderID = OrderRelation::MAGENTO_ORDER_ID;

        $select = $this->_connection->select()
            ->from(
                ['so' => $this->getTableName('sales_order')],
                []
            )
            ->join(
                ['mor' => $this->getTableName(OrderRelation::TABLE)],
                "mor.{$fkOrderID} = so.entity_id",
                []
            )
            ->where('mor.'. OrderRelation::IS_MAIN_ORDER . ' = ?', 0)
            ->where('mor.'. OrderRelation::SELLER_ID . ' IS NOT NULL')
            ->where('so.status = ?', \Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface::STATUS_PENDING_APPROVAL)
            ->where('TIMESTAMPDIFF(DAY, so.created_at, NOW()) >= ?', $this->days)
            ->columns([
                ExpiredData::SELLER_ID => 'mor.' . OrderRelation::SELLER_ID,
                ExpiredData::ORDER_IDS => new \Zend_Db_Expr('GROUP_CONCAT(so.entity_id)')
            ])
            ->group('mor.'. OrderRelation::SELLER_ID);

        if (!empty($incrementIds)) {
            $select->where('so.increment_id IN (?)', $incrementIds);
        }

        return $this->_connection->fetchAll($select);
    }

    /**
     * Get table name with prefix if exist
     * @param string $tableName
     * @return string
     */
    private function getTableName(string $tableName): string {
        return $this->_connection->getTableName($tableName);
    }
}
