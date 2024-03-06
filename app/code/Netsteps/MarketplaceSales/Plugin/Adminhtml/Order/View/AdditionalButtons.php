<?php
/**
 * AdditionalButtons
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Adminhtml\Order\View;

use Magento\Framework\DataObject;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Traits\OrderDataManagementTrait;
use Netsteps\Seller\Model\Admin\SellerManagementInterface as AdminSellerManagement;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * Class AdditionalButtons
 * @package Netsteps\Marketplace\Plugin\Adminhtml\Order\View
 */
class AdditionalButtons
{
    use OrderDataManagementTrait;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var AdminSellerManagement
     */
    private AdminSellerManagement $_sellerManagement;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @param AdminSellerManagement $adminSellerManagement
     * @param EventManager $eventManager
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        AdminSellerManagement $adminSellerManagement,
        EventManager          $eventManager,
        LoggerPool            $loggerPool
    )
    {
        $this->_sellerManagement = $adminSellerManagement;
        $this->_eventManager = $eventManager;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * Add additional buttons
     * @param \Magento\Sales\Block\Adminhtml\Order\View $view
     * @return void|null
     */
    public function beforeSetLayout(
        \Magento\Sales\Block\Adminhtml\Order\View $view
    )
    {

        $order = $view->getOrder();
        $relation = $this->getOrderRelation($order);

        if (
            $view->getNameInLayout() !== 'sales_order_edit' ||
            !$relation ||
            $relation->getIsMainOrder()
        ) {
            return null;
        }

        $transportObject = new DataObject([
            'show_approve_button' => true,
            'show_decline_button' => true,
            'show_export_order_button' => true
        ]);

        /**
         * Dispatch event to be observed from other modules and set the buttons
         * approve/decline as not visible
         */
        $this->_eventManager->dispatch(
            'marketplace_sales_admin_before_add_buttons',
            ['order' => $order, 'relation' => $relation, 'transport' => $transportObject]
        );

        if ($this->canApprove($order) && $transportObject->getShowApproveButton()) {
            $view->addButton(
                'order_approve',
                [
                    'label' => __('Approve'),
                    'class' => 'order-approve',
                    'id' => 'order-view-approve-button',
                    'onclick' => "setLocation('{$view->getUrl('marketplace_sales/order/approve')}')"
                ],
                0,
                10
            );
        }

        if ($this->canDecline($order) && $transportObject->getShowDeclineButton()) {
            $view->addButton(
                'order_decline',
                [
                    'label' => __('Decline'),
                    'class' => 'order-decline',
                    'id' => 'order-view-decline-button',
                    'onclick' => "setLocation('{$view->getUrl('marketplace_sales/order/decline')}')"
                ],
                0,
                11
            );
        }

        if ($transportObject->getShowExportOrderButton()) {
            $view->addButton(
                'export_order_json',
                [
                    'label' => __('Export as JSON'),
                    'class' => 'order-export-json',
                    'id' => 'order-view-export-order-button',
                    'onclick' => "setLocation('{$view->getUrl('marketplace_sales/order/export')}')"
                ],
                0,
                100
            );
        }

        /**
         * Remove cancel and credit memo for not approved split orders
         */
        if ($this->canApprove($order) || $this->canDecline($order)) {
            $view->removeButton('order_cancel');
            $view->removeButton('order_creditmemo');
        }
    }
}
