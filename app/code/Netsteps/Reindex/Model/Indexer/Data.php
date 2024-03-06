<?php
/**
 * Data
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Reindex
 */

namespace Netsteps\Reindex\Model\Indexer;
use Magento\Framework\DataObject;
use Netsteps\Reindex\Api\Data\IndexerDataInterface;

/**
 * Class Data
 * @package Netsteps\Reindex\Model\Indexer
 */
class Data extends DataObject implements IndexerDataInterface
{

    /**
     * @inheritDoc
     */
    public function getIndexerId(): string
    {
        return $this->_getData(self::INDEXER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getEntityIds(): array
    {
        $ids = $this->_getData(self::ENTITY_IDS) ?? [];

        if (!is_array($ids)) {
            $ids = [$ids];
        }
        return array_map([$this, '_castToInt'], $ids);
    }

    /**
     * @inheritDoc
     */
    public function setIndexerId(string $indexerId): \Netsteps\Reindex\Api\Data\IndexerDataInterface
    {
        return $this->setData(self::INDEXER_ID, $indexerId);
    }

    /**
     * @inheritDoc
     */
    public function setEntityIds(array $entityIds): \Netsteps\Reindex\Api\Data\IndexerDataInterface
    {
        return $this->setData(self::ENTITY_IDS, $entityIds);
    }

    /**
     * Cast item to int
     * @param $item
     * @return int
     */
    public function _castToInt($item): int {
        return (int)$item;
    }
}
