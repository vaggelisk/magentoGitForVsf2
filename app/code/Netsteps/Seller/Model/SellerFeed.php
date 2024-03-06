<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Model\AbstractModel;
use Netsteps\Seller\Api\Data\SellerFeedInterface;
use Netsteps\Seller\Model\Config\FeedTypeOptionsSource;

class SellerFeed extends AbstractModel implements SellerFeedInterface
{
    protected $_eventPrefix = 'ns_seller_feed';

    protected $_eventObject = 'seller_feed';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\ResourceModel\SellerFeed::class);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId(): int
    {
        return $this->getData(SellerFeedInterface::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId(int $id): \Netsteps\Seller\Api\Data\SellerFeedInterface
    {
        $this->setData(SellerFeedInterface::SELLER_ID, $id);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->getData(SellerFeedInterface::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): \Netsteps\Seller\Api\Data\SellerFeedInterface
    {
        if(!in_array($type, FeedTypeOptionsSource::AVAILABLE_TYPES)){
            $type = null;
        }
        $this->setData(SellerFeedInterface::TYPE, $type);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUrlPath(): string
    {
        return (string)$this->getData(SellerFeedInterface::URL_PATH);
    }

    /**
     * @inheritDoc
     */
    public function setUrlPath(string $url): \Netsteps\Seller\Api\Data\SellerFeedInterface
    {
       $this->setData(SellerFeedInterface::URL_PATH, $url);
       return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStoreId(): int
    {
       return $this->getData(SellerFeedInterface::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId(int $id): \Netsteps\Seller\Api\Data\SellerFeedInterface
    {
        $this->setData(SellerFeedInterface::STORE_ID, $id);
        return $this;
    }
}
