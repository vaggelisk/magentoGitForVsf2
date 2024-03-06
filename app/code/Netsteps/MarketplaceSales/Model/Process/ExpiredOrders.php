<?php
/**
 * ExpiredOrders
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Process;

use Netsteps\MarketplaceSales\Model\Order\Email\Sender\PendingApproval;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Sales\Api\Data\OrderInterfaceFactory as OrderFactory;
use Netsteps\MarketplaceSales\Model\Process\Resource\OrderDataRepositoryInterface as OrderDataRepository;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder as SearchCriteriaBuilder;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Psr\Log\LoggerInterface;

/**
 * Class ExpiredOrders
 * @package Netsteps\MarketplaceSales\Model\Process
 */
class ExpiredOrders
{
    /**
     * @var OrderDataRepository
     */
    private OrderDataRepository $_orderDataRepository;

    /**
     * @var OrderFactory
     */
    private OrderFactory $_orderFactory;

    /**
     * @var PendingApproval
     */
    private PendingApproval $_sender;

    /**
     * @var OrderRepository
     */
    private OrderRepository $_orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param OrderDataRepository $orderDataRepository
     * @param OrderFactory $orderFactory
     * @param PendingApproval $sender
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SellerRepository $sellerRepository
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        OrderDataRepository $orderDataRepository,
        OrderFactory $orderFactory,
        PendingApproval $sender,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SellerRepository $sellerRepository,
        LoggerPool $loggerPool
    ) {
        $this->_orderDataRepository = $orderDataRepository;
        $this->_orderFactory = $orderFactory;
        $this->_sender = $sender;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sellerRepository = $sellerRepository;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * Send notification to sellers for pending approval that need action.
     * @return void
     */
    public function execute(): void {
        $allPendingApproval = $this->_orderDataRepository->getExpiredPendingApproval();

        /** @var Order $fakeOrder */
        $fakeOrder = $this->_orderFactory->create();
        $fakeOrder->setStoreId(1);

        foreach ($allPendingApproval as $pendingApproval) {
            if (!$pendingApproval->getSellerId() || empty($pendingApproval->getOrderIds())) {
                continue;
            }

            $criteria = $this->_searchCriteriaBuilder
                ->addFilter('entity_id', $pendingApproval->getOrderIds(), 'in')
                ->create();
            $orders = $this->_orderRepository->getList($criteria)->getItems();

            if (count($orders) === 0){
                continue;
            }

            try {
                $seller = $this->_sellerRepository->getById($pendingApproval->getSellerId());
                $this->_sender->setExpiredInformation($seller, $orders)
                    ->send($fakeOrder);
            } catch (\Exception $e) {
                $this->_logger->error(
                    __(
                        'Can not send notification email for pending approval orders for seller with id = %1. Reason: %2',
                        [$pendingApproval->getSellerId(), $e->getMessage()]
                    )
                );
            }
        }
    }
}
