<?php
/**
 * AddSellerInventoryDataToRequestItems
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Ampersand\Stock;

use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Traits\OrderItemDataManagementTrait;
use Psr\Log\LoggerInterface;

/**
 * Class AddSellerInventoryDataToRequestItems
 * @package Netsteps\MarketplaceSales\Plugin\Ampersand\Stock
 */
class AddSellerInventoryDataToRequestItems
{
    use OrderItemDataManagementTrait;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param LoggerPool $loggerPool
     */
    public function __construct(LoggerPool $loggerPool)
    {
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @param \Ampersand\DisableStockReservation\Model\GetInventoryRequestFromOrder $subject
     * @param InventoryRequestInterface $resultRequest
     * @param OrderInterface $order
     * @param array $requestItems
     * @return InventoryRequestInterface
     */
    public function afterExecute(
        \Ampersand\DisableStockReservation\Model\GetInventoryRequestFromOrder $subject,
        InventoryRequestInterface $resultRequest,
        OrderInterface $order,
        array $requestItems
    ): InventoryRequestInterface {

        $sellerItems = $this->getSellerOrderItems($order);

        foreach ($resultRequest->getItems() as $item){
            if (array_key_exists($item->getSku(), $sellerItems)){
                $item->addData($sellerItems[$item->getSku()]);
            }
        }

        return $resultRequest;
    }

    /**
     * Get order items with seller data
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    private function getSellerOrderItems(\Magento\Sales\Model\Order $order): array
    {
        $items = [];

        /** @var  $item \Magento\Sales\Model\Order\Item */
        foreach ($order->getItems() as $item) {
            $infoBuyRequest = $item->getProductOptionByCode('info_buyRequest');

            if (!$infoBuyRequest){
                $this->_logger->warning(
                    __(
                        'Stock Warning for order %1: Can not find info_buyRequest for order item %2',
                        [$order->getIncrementId(), $item->getSku()]
                    )
                );
                continue;
            }

            $sellerInfoData = $this->exportSellerInfoData($item);

            if (!empty($sellerInfoData)){
                $items[$item->getSku()] = $sellerInfoData;
            }
        }

        return $items;
    }
}
