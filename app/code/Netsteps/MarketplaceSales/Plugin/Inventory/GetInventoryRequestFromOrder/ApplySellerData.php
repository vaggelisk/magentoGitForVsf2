<?php
/**
 * ApplySellerData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Inventory\GetInventoryRequestFromOrder;

use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Traits\OrderItemDataManagementTrait;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class ApplySellerData
 * @package Netsteps\MarketplaceSales\Plugin\Inventory\GetInventoryRequestFromOrder
 */
class ApplySellerData
{
    use OrderItemDataManagementTrait;

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_orderRelationRepository;

    /**
     * @var Logger
     */
    private Logger $_logger;

    /**
     * @param OrderRelationRepository $orderRelationRepository
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        OrderRelationRepository $orderRelationRepository,
        LoggerPool $loggerPool
    )
    {
        $this->_orderRelationRepository = $orderRelationRepository;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @param \Magento\InventorySourceSelectionApi\Model\GetInventoryRequestFromOrder $getInventoryRequestFromOrder
     * @param InventoryRequestInterface $inventoryRequest
     * @param int $orderId
     * @param array $requestItems
     * @return InventoryRequestInterface
     */
    public function afterExecute(
        \Magento\InventorySourceSelectionApi\Model\GetInventoryRequestFromOrder $getInventoryRequestFromOrder,
        InventoryRequestInterface $inventoryRequest,
        int $orderId,
        array $requestItems
    ): InventoryRequestInterface
    {
        if ($parentOrder = $this->_orderRelationRepository->getParentOrderByOrderId($orderId, true)) {
            $sellerInfoDataMap = $this->getSellerDataItemMap($parentOrder);

            foreach ($inventoryRequest->getItems() as $item) {
                if (array_key_exists($item->getSku(), $sellerInfoDataMap)){
                    $item->addData($sellerInfoDataMap[$item->getSku()]);
                }
            }
        }

        return $inventoryRequest;
    }

    /**
     * Get seller data item map
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    private function getSellerDataItemMap(\Magento\Sales\Model\Order $order): array {
        $map = [];

        foreach ($order->getItems() as $item){
            $sellerInfoData = $this->exportSellerInfoData($item);

            if (!empty($sellerInfoData)) {
                $map[$item->getSku()] =$sellerInfoData;
            }
        }

        return $map;
    }
}
