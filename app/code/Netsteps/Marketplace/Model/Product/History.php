<?php
/**
 * History
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

use Magento\Framework\Model\AbstractModel;
use Netsteps\Marketplace\Api\Data\ProductHistoryInterface;
use Netsteps\Marketplace\Model\ResourceModel\Product\History as ResourceModel;

/**
 * Class History
 * @package Netsteps\Marketplace\Model\Product
 */
class History extends AbstractModel implements ProductHistoryInterface
{
    protected $_idFieldName = self::ID;

    protected $_eventObject = self::EVENT_OBJECT;

    protected $_eventPrefix = self::EVENT_PREFIX;

    protected $_cacheTag = self::CACHE_TAG;


    /**
     * Initialize resource
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku(): string
    {
        return $this->getId();
    }

    /**
     * @inheritDoc
     */
    public function getVersionCode(): string
    {
        return $this->_getData(self::VERSION_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku(string $sku): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
    {
        return $this->setData(self::ID, $sku);
    }

    /**
     * @inheritDoc
     */
    public function setVersionCode(string $versionCode): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
    {
        return $this->setData(self::VERSION_CODE, $versionCode);
    }
}
