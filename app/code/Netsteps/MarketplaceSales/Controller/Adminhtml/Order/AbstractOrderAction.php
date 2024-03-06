<?php
/**
 * AbstractOrderAction
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Framework\App\Response\Http\FileFactory as FileFactory;
use Magento\Framework\Webapi\ServiceOutputProcessor as OutputProcessor;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Api\OrderStatusManagementInterface as OrderStatusManagement;
use Netsteps\MarketplaceSales\Traits\OrderDataManagementTrait;
use Netsteps\Seller\Model\Admin\SellerManagementInterface as AdminSellerManagement;
use Netsteps\MarketplaceSales\Api\OrderManagementInterface as OrderDataManagement;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractOrderAction
 * @package Netsteps\MarketplaceSales\Controller\Adminhtml
 */
abstract class AbstractOrderAction extends \Magento\Backend\App\Action
{
    use OrderDataManagementTrait;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $_logger;

    /**
     * @var OrderStatusManagement
     */
    protected OrderStatusManagement $_orderStatusManagement;

    /**
     * @var OrderRepository
     */
    protected OrderRepository $_orderRepository;

    /**
     * @var AdminSellerManagement
     */
    private AdminSellerManagement $_sellerManagement;

    /**
     * @var FileFactory
     */
    protected FileFactory $_fileFactory;

    /**
     * @var OrderDataManagement
     */
    protected OrderDataManagement $_orderDataManagement;

    /**
     * @var OutputProcessor
     */
    protected OutputProcessor $_outputProcessor;

    /**
     * @param Context $context
     * @param OrderRepository $orderRepository
     * @param OrderStatusManagement $orderStatusManagement
     * @param OrderDataManagement $orderDataManagement
     * @param OutputProcessor $outputProcessor
     * @param AdminSellerManagement $sellerManagement
     * @param FileFactory $fileFactory
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        Context               $context,
        OrderRepository       $orderRepository,
        OrderStatusManagement $orderStatusManagement,
        OrderDataManagement   $orderDataManagement,
        OutputProcessor       $outputProcessor,
        AdminSellerManagement $sellerManagement,
        FileFactory           $fileFactory,
        LoggerPool            $loggerPool
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_orderStatusManagement = $orderStatusManagement;
        $this->_sellerManagement = $sellerManagement;
        $this->_fileFactory = $fileFactory;
        $this->_orderDataManagement = $orderDataManagement;
        $this->_outputProcessor = $outputProcessor;
        $this->_logger = $loggerPool->getLogger('order');
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectPath = 'sales/order/view';
        $params = [];

        try {
            $order = $this->getOrder();

            if (!$this->_isAllowedActionForOrder($order)) {
                throw new LocalizedException(
                    __('You are not authorized to have access for this order')
                );
            }

            /**
             * Main action called from child classes
             */
            $this->_execute($order);

            $params['order_id'] = $order->getEntityId();
            return $this->getResultOrResponse() ?? $this->_redirect($redirectPath, $params);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_logger->error(__('Admin action approve order error: %1', $e->getMessage()));

            if (!isset($order)) {
                $redirectPath = 'sales/order/index';
            } else {
                $params['order_id'] = $order->getId();
            }

            return $this->_redirect($redirectPath, $params);
        }
    }

    /**
     * Get order
     * @return OrderInterface
     * @throws LocalizedException
     */
    protected function getOrder(): OrderInterface
    {
        $orderId = $this->_request->getParam('order_id');

        if (!$orderId) {
            throw new LocalizedException(
                __('Order ID is a required parameter.')
            );
        }

        return $this->_orderRepository->get($orderId);
    }

    /**
     * Get logged in user seller id
     * @return int|null
     */
    protected function getUserSellerId(): ?int
    {
        return $this->_sellerManagement->getLoggedSellerId();
    }

    /**
     * Check if is allowed an action for order
     * @param OrderInterface $order
     * @return bool
     */
    protected function _isAllowedActionForOrder(OrderInterface $order): bool
    {
        $relation = $this->getOrderRelation($order);

        if (!$relation || !$relation->getRelationId() || $relation->getIsMainOrder() || !$relation->getSellerId()) {
            return false;
        }

        /** @var  $user \Magento\User\Model\User */
        $user = $this->_auth->getUser();
        return $relation->getSellerId() === $this->getUserSellerId() || (int)$user->getRole()->getId() === 1;
    }

    /**
     * Get current admin user full name
     * @return string
     */
    protected function getCurrentUserFullName(): string
    {
        /** @var  $user \Magento\User\Model\User */
        $user = $this->_auth->getUser();
        return $user->getName();
    }

    /**
     * Execute the base action
     * @param OrderInterface $order
     * @return void
     */
    abstract protected function _execute(\Magento\Sales\Api\Data\OrderInterface $order): void;

    /**
     * Get custom result or response
     * Override this method if you want to send another response
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|null
     */
    protected function getResultOrResponse(): mixed
    {
        return null;
    }
}
