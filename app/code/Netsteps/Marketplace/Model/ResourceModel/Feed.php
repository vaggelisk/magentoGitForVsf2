<?php
/**
 * Feed
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Netsteps\Marketplace\Api\Data\FeedInterface as Model;

/**
 * Class Feed
 * @package Netsteps\Marketplace\Model\ResourceModel
 */
class Feed extends AbstractDb
{
    protected $_idFieldName = Model::ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Model::TABLE, Model::ID);
    }

    /**
     * Override method to compress feed_data field.
     * @param DataObject $object
     * @param $table
     * @return array
     */
    protected function _prepareDataForTable(DataObject $object, $table): array
    {
        $data = parent::_prepareDataForTable($object, $table);

        if (isset($data[Model::FEED_DATA]) && is_string($data[Model::FEED_DATA])){
            $value = $data[Model::FEED_DATA];
            $data[Model::FEED_DATA] = new \Zend_Db_Expr("COMPRESS('{$value}')");
        }

        return$data;
    }

    /**
     * Override method to un-compress feed data during load
     * @param $field
     * @param $value
     * @param $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object): \Magento\Framework\DB\Select
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->columns([Model::FEED_DATA => new \Zend_Db_Expr('UNCOMPRESS('.Model::FEED_DATA.')')]);
        return $select;
    }

    /**
     * Prepare data for update override to compress feed data
     * @param $object
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareDataForUpdate($object)
    {
        $data = parent::prepareDataForUpdate($object);

        if (isset($data[Model::FEED_DATA]) && is_string($data[Model::FEED_DATA])){
            $value = $data[Model::FEED_DATA];
            $data[Model::FEED_DATA] = new \Zend_Db_Expr("COMPRESS('{$value}')");
        }

        return $data;
    }
}
