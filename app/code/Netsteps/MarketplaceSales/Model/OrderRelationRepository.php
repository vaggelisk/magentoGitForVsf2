<?php
/**
 * OrderRelationRepository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Marketplace\Exception\InvalidValueException;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterfaceFactory as OrderRelationFactory;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface as OrderRelation;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderRelation as RelationResource;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderRelation\CollectionFactory as RelationCollectionFactory;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderRelation\Collection as RelationCollection;
use Magento\Framework\Api\SearchCriteriaBuilder as SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OrderRelationRepository
 * @package Netsteps\MarketplaceSales\Model
 */
class OrderRelationRepository implements OrderRelationRepositoryInterface
{
    /**
     * @var OrderRelation[]
     */
    private array $relations = [];

    /**
     * @var OrderRelationFactory
     */
    private OrderRelationFactory $_relationFactory;

    /**
     * @var RelationCollectionFactory
     */
    private RelationCollectionFactory $_relationCollectionFactory;

    /**
     * @var RelationResource
     */
    private RelationResource $_relationResource;

    /**
     * @var OrderRepository
     */
    private OrderRepository $_orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param OrderRelationFactory $relationFactory
     * @param RelationCollectionFactory $collectionFactory
     * @param RelationResource $resource
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        OrderRelationFactory $relationFactory,
        RelationCollectionFactory $collectionFactory,
        RelationResource $resource,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerPool $loggerPool
    )
    {
        $this->_relationFactory = $relationFactory;
        $this->_relationCollectionFactory = $collectionFactory;
        $this->_relationResource = $resource;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @inheritDoc
     */
    public function save(OrderRelation $orderRelation): OrderRelation
    {
        $this->_relationResource->save($orderRelation);
        return $orderRelation;
    }

    /**
     * @inheritDoc
     */
    public function getChildrenOrders(OrderInterface $order): array
    {
        /**  @var $relations RelationCollection */
        $relations = $this->_relationCollectionFactory->create();
        $relations->addIsChildFilter()
            ->addParentOrderFilter($order->getEntityId());

        $orderIds = $relations->getColumnValues(OrderRelation::MAGENTO_ORDER_ID);

        if (empty($orderIds)){
            return [];
        }

        $this->_searchCriteriaBuilder->addFilter('entity_id', $orderIds, 'in');
        $orders = $this->_orderRepository->getList($this->_searchCriteriaBuilder->create());
        return $orders->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getChildrenOrdersByParentId(int $parentId): array
    {
        $parentOrder = $this->_orderRepository->get($parentId);
        return $this->getChildrenOrders($parentOrder);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function linkOrders(OrderInterface $order, array $orders): array
    {
        $ids = [];

        foreach ($orders as $splitOrder) {
            $relation = $this->createNewRelation();

            $relation->setIsProcessed(true)
                ->setIsMainOrder(false)
                ->setMagentoOrderId($splitOrder->getEntityId())
                ->setParentOrderId($order->getEntityId())
                ->setSellerId($splitOrder->getData('seller_id'));

            $this->_relationResource->save($relation);

            $ids[] = $relation->getRelationId();
        }

        return $ids;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function registerOrder(OrderInterface $order): int
    {
        $orderId = (int)$order->getEntityId();

        try {
            $relation = $this->getRelationByOrderId($orderId);
            return $relation->getRelationId();
        } catch (NoSuchEntityException $e) {
            return $this->_register($order);
        } catch (\Exception $e) {
            $this->_logger->critical(
                __('Error on register a new order for order %1. Reason: %2', [$order->getIncrementId(), $e->getMessage()])
            );

            throw new LocalizedException(
                __('Something went wrong with order registration')
            );
        }
    }

    /**
     * Register new order
     * @param OrderInterface $order
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function _register(OrderInterface $order): int {
        $relation = $this->createNewRelation()
            ->setMagentoOrderId((int)$order->getEntityId())
            ->setIsMainOrder(true);

        $this->_relationResource->save($relation);

        return $relation->getRelationId();
    }

    /**
     * @inheritDoc
     */
    public function getParentOrder(OrderInterface $order, bool $useFallback = false): ?OrderInterface
    {
        try {
            $relation = $this->getRelationByOrderId($order->getEntityId());
        } catch (\Exception $e) {
            return null;
        }

        $parentOrderId = $relation->getParentOrderId();

        if (!$parentOrderId && $useFallback){
            $parentOrderId = $order->getEntityId();
        }

        if (!$parentOrderId){
            return null;
        }

        return $this->_orderRepository->get($parentOrderId);
    }

    /**
     * Get parent order by order id
     * @param int $orderId
     * @param bool $useFallback
     * @return OrderInterface|null
     */
    public function getParentOrderByOrderId(int $orderId, bool $useFallback = false): ?OrderInterface
    {
        $order = $this->_orderRepository->get($orderId);
        return $this->getParentOrder($order, $useFallback);
    }

    /**
     * Create an empty order relation object
     * @return OrderRelation
     */
    private function createNewRelation(): OrderRelation {
        return $this->_relationFactory->create();
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getRelationByOrderId(int $orderId): ?OrderRelation
    {
        $orderRelation = $this->createNewRelation();
        $this->_relationResource->load($orderRelation, $orderId, OrderRelation::MAGENTO_ORDER_ID);

        if (!$orderRelation->getRelationId()) {
            throw new NoSuchEntityException(
                __('No relation found for order %1', $orderId)
            );
        }

        return $orderRelation;
    }

    /**
     * @inheritDoc
     */
    public function getMainOrders(?bool $processedStatus = null): array
    {
        /** @var  $orderRelations RelationResource\Collection */
        $orderRelations = $this->_relationCollectionFactory->create();
        $orderRelations->addIsMainFilter();

        if (!is_null($processedStatus)) {
            $orderRelations->addProcessedFilter($processedStatus);

            if ($processedStatus === false) {
                $orderRelations->addFieldToFilter(OrderRelation::NUM_OF_TRIES, ['lteq' => 3]);
            }
        }

        return $orderRelations->getItems();
    }
}
