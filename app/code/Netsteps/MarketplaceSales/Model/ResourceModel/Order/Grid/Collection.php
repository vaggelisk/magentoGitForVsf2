<?php
/**
 * Collection
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\ResourceModel\Order\Grid;

use Magento\Framework\App\ObjectManager;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;
use Netsteps\Seller\Model\Admin\SellerManagementInterface as AdminSellerManagement;
use Magento\Backend\Model\Auth\Session as AdminSession;

/**
 * Class Collection
 * @package Netsteps\MarketplaceSales\Model\ResourceModel\Order\Grid
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    /**
     * @var AdminSellerManagement
     */
    private AdminSellerManagement $_adminSellerManagement;

    /**
     * @var AdminSession
     */
    private AdminSession $_adminSession;

    /**
     * Initialize admin seller management object without override __construct method
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $objectManager = ObjectManager::getInstance();
        $this->_adminSellerManagement = $objectManager->get(AdminSellerManagement::class);
        $this->_adminSession = $objectManager->get(AdminSession::class);
    }

    /**
     * Override initial select statement to add additional base filtering
     * On which seller is currently logged
     * @return $this|\Magento\Sales\Model\ResourceModel\Order\Grid\Collection|Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $sellerId = $this->_adminSellerManagement->getLoggedSellerId() ?? 0;

        /**
         * Check if curren user is administrator
         */
        if (1 === (int)$this->_adminSession->getUser()->getRole()->getId() || !$sellerId) {
            return $this;
        }

        $this->getSelect()
            ->join(
                ['mor' => $this->getTable(OrderRelationInterface::TABLE)],
                "main_table.entity_id = mor.magento_order_id",
                [OrderRelationInterface::IS_MAIN_ORDER, OrderRelationInterface::IS_PROCESSED]
            )
            ->where(
                'mor.seller_id = ?', $sellerId
            );

        return $this;
    }
}
