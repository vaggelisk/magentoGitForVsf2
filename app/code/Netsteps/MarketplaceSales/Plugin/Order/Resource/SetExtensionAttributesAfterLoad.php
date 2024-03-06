<?php
/**
 * SetExtensionAttributesAfterLoad
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Order\Resource;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;
use Netsteps\MarketplaceSales\Api\OrderItemRegistryRepositoryInterface as OrderItemRegistryRepository;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Api\Data\OrderBasicDataInterface as OrderBasicData;
use Netsteps\MarketplaceSales\Api\Data\OrderBasicDataInterfaceFactory as OrderBasicDataFactory;
use Psr\Log\LoggerInterface;

/**
 * Class SetExtensionAttributesAfterLoad
 * @package Netsteps\MarketplaceSales\Plugin\Order\Resource
 */
class SetExtensionAttributesAfterLoad
{
    /**
     * @var OrderItemRegistryRepository
     */
    private OrderItemRegistryRepository $_orderItemRegistryRepository;

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_relationRepository;

    /**
     * @var OrderBasicDataFactory
     */
    private OrderBasicDataFactory $_orderBasicDataFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param OrderItemRegistryRepository $orderItemRegistryRepository
     * @param OrderRelationRepository $orderRelationRepository
     * @param OrderBasicDataFactory $orderBasicDataFactory
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        OrderItemRegistryRepository $orderItemRegistryRepository,
        OrderRelationRepository $orderRelationRepository,
        OrderBasicDataFactory $orderBasicDataFactory,
        LoggerPool $loggerPool
    ) {
        $this->_orderItemRegistryRepository = $orderItemRegistryRepository;
        $this->_relationRepository = $orderRelationRepository;
        $this->_orderBasicDataFactory = $orderBasicDataFactory;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * After load order set related extension attributes
     * @param \Magento\Sales\Model\ResourceModel\Order $resourceOrder
     * @param $result
     * @param \Magento\Sales\Api\Data\OrderInterface $object
     * @param $value
     * @param $field
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterLoad(
        \Magento\Sales\Model\ResourceModel\Order $resourceOrder,
        $result,
        \Magento\Sales\Api\Data\OrderInterface $object,
        $value,
        $field = null
    ): \Magento\Sales\Model\ResourceModel\Order {

        if (!$object->getEntityId()){
            return $result;
        }

        try {
            $orderId = $object->getEntityId();
            $sellerItems = $this->_orderItemRegistryRepository->getRegistriesByOrderId($orderId);
            $extensionAttributes = $object->getExtensionAttributes();
            $extensionAttributes->setSellerItems($sellerItems);
            $extensionAttributes->setNumberOfDeliveries($this->getDeliveryNumber($sellerItems));

            try {
                $relationInfo = $this->_relationRepository->getRelationByOrderId($orderId);

                $extensionAttributes->setMarketplaceRelation($relationInfo);
                $extensionAttributes->setChildOrders($this->getChildOrdersDataArray($object));
            } catch (NoSuchEntityException $e) {/** Dont need any action here */}

            $object->setExtensionAttributes($extensionAttributes);
        } catch (\Exception $e) {
            $this->_logger->critical(
                __(
                    'Can not set extension attributes in order %1. Reason: ',
                    [$object->getEntityId(), $e->getMessage()]
                )
            );
        }

        return $result;
    }

    /**
     * Get child order basic data
     * @param \Magento\Sales\Model\Order $order
     * @return OrderBasicData[]
     */
    private function getChildOrdersDataArray(\Magento\Sales\Model\Order $order): array {
        $orders = [];

        foreach ($this->_relationRepository->getChildrenOrders($order) as $childOrder) {
            /** @var  $orderData OrderBasicData */
            $orderData = $this->_orderBasicDataFactory->create();
            $orders[] = $orderData->populateFromOrder($childOrder);
        }

        return $orders;
    }

    /**
     * @param OrderItemRegistryInterface[] $items
     * @return int
     */
    private function getDeliveryNumber(array $items): int {
        $sellers = [];

        foreach ($items as $item) {
            $sellers[] = $item->getItemSellerId();
        }

        return count(array_unique($sellers));
    }
}
