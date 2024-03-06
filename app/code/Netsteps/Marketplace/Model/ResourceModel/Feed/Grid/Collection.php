<?php
/**
 * Collection
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\ResourceModel\Feed\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Netsteps\Marketplace\Api\Data\FeedInterface;
use Netsteps\Marketplace\Model\ResourceModel\Feed;
use Psr\Log\LoggerInterface as Logger;

use Netsteps\Seller\Model\Admin\SellerManagementInterface as AdminSellerManagement;


/**
 * Class Collection
 * @package Netsteps\Marketplace\Model\ResourceModel\Feed\Grid
 */
class Collection extends SearchResult
{
    /**
     * @var AdminSellerManagement
     */
    private AdminSellerManagement $_adminSellerManagement;

    protected $_initialFieldsToSelect = [
        FeedInterface::ID,
        FeedInterface::STATUS,
        FeedInterface::FEED_TYPE,
        FeedInterface::FILE_TYPE,
        FeedInterface::SELLER_ID,
        FeedInterface::CREATED_AT,
        FeedInterface::UPDATED_AT
    ];

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @param AdminSellerManagement $adminSellerManagement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        AdminSellerManagement $adminSellerManagement,
        $mainTable = FeedInterface::TABLE,
        $resourceModel = Feed::class
    )
    {
        $this->_adminSellerManagement = $adminSellerManagement;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * Override init select to get only current seller id feeds
     * @return $this|Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        if ($sellerId = $this->_adminSellerManagement->getLoggedSellerId()){
            $this->addFieldToFilter('seller_id', $sellerId);
        }

        $this->removeAllFieldsFromSelect();
        return $this;
    }
}
