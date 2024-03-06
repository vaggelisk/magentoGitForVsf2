<?php
/**
 * Collection
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\ResourceModel\Feed;

use JetBrains\PhpStorm\Pure;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

use Netsteps\Marketplace\Model\ResourceModel\Feed as ResourceModel;
use Netsteps\Marketplace\Model\Feed as Model;

/**
 * Class Collection
 * @package Netsteps\Marketplace\Model\ResourceModel\Feed
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize model and resource
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    /**
     * Add seller filter to collection
     * @param int $sellerId
     * @return $this
     */
    public function addSellerFilter(int $sellerId): \Netsteps\Marketplace\Model\ResourceModel\Feed\Collection {
        if (!$this->hasFlag('seller_filter')) {
            $this->addFieldToFilter(Model::SELLER_ID, ['eq' => $sellerId]);
            $this->setFlag('seller_filter', true);
        }

        return $this;
    }

    /**
     * Add multiple seller filter
     * @param array $sellerIds
     * @return Collection
     */
    public function addSellersFilter(array $sellerIds): \Netsteps\Marketplace\Model\ResourceModel\Feed\Collection {
        if (!$this->hasFlag('sellers_filter')) {
            $this->addFieldToFilter(Model::SELLER_ID, ['in' => $sellerIds]);
            $this->setFlag('sellers_filter', true);
        }

        return $this;
    }

    /**
     * Override method to un compress feed data
     * @return Collection
     */
    protected function _beforeLoad(): Collection
    {
        $this->getSelect()->columns(
            [Model::FEED_DATA => new \Zend_Db_Expr('UNCOMPRESS('.Model::FEED_DATA.')')]
        );
        return parent::_beforeLoad();
    }
}
