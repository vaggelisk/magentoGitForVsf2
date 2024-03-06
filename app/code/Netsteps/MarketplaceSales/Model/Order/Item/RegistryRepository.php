<?php
/**
 * RegistryRepository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Item;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Marketplace\Api\Data\MerchantDataInterface;
use Netsteps\Marketplace\Model\Data\MerchantItemData;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface;
use Netsteps\MarketplaceSales\Api\OrderItemRegistryRepositoryInterface;
use Netsteps\MarketplaceSales\Exception\Quote\ValidationException;
use Netsteps\MarketplaceSales\Model\Product\SellerManagementInterface;
use Netsteps\MarketplaceSales\Traits\ProductDataManagementTrait;
use Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterface as OrderItemRegistry;
use Netsteps\MarketplaceSales\Api\Data\OrderItemRegistryInterfaceFactory as OrderItemRegistryFactory;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderItemRegistry as ResourceModel;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderItemRegistry\CollectionFactory as CollectionFactory;
use Netsteps\MarketplaceSales\Model\ResourceModel\OrderItemRegistry\Collection as RegistryCollection;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\Quote\Address\Item as QuoteAddressItem;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RegistryRepository
 * @package Netsteps\MarketplaceSales\Model\Order\Item
 */
class RegistryRepository implements OrderItemRegistryRepositoryInterface
{
    use ProductDataManagementTrait;

    /**
     * @var OrderItemRegistryFactory
     */
    private OrderItemRegistryFactory $_modelFactory;

    /**
     * @var ResourceModel
     */
    private ResourceModel $_resource;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $_collectionFactory;

    /**
     * @param OrderItemRegistryFactory $modelFactory
     * @param CollectionFactory $collectionFactory
     * @param ResourceModel $resource
     */
    public function __construct(
        OrderItemRegistryFactory $modelFactory,
        CollectionFactory $collectionFactory,
        ResourceModel $resource
    )
    {
        $this->_modelFactory = $modelFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_resource = $resource;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @throws ValidationException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function register(
        \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $cartItem,
        \Magento\Sales\Api\Data\OrderItemInterface $orderItem
    ): ?int
    {
        if (!in_array($cartItem->getProductType(), MerchantItemData::getAvailableProductTypesToIndex())) {
            return null;
        }

        if (!$cartItem->getId()) {
            throw new LocalizedException(
                __('Can not register a cart item without id')
            );
        }

        if (!$orderItem->getId()) {
            throw new LocalizedException(
                __('Can not register an order item without id')
            );
        }

        try {
            $registry = $this->getByQuoteItemId($cartItem->getId());
            if ($registry->getOrderItemId() !== (int)$orderItem->getItemId()) {
                throw new LocalizedException(
                    __('Registry for quote item %1 already exists for another order item.', $cartItem->getId())
                );
            }
        } catch (NoSuchEntityException $e) {
            $registry = $this->createNewRegistry();
            $registry->setQuoteItemId($cartItem->getId());
        }

        $sellerInfo = $this->getSellerInfoFromQuoteItem($cartItem);
        $splitParentItemId = $this->getBuyInfoRequestData($cartItem)[SellerManagementInterface::PARENT_ITEM_ID] ?? false;

        if (empty($sellerInfo) || $splitParentItemId !== false){
            return null;
        }

        $this->validateSellerInfo($sellerInfo);
        $this->populateRegistryWithSellerData($registry, $sellerInfo);

        $registry->setOrderItemId((int)$orderItem->getItemId())
            ->setOrderId((int)$orderItem->getOrderId());

        $this->_resource->save($registry);

        return $registry->getRegistryId();
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getByQuoteItemId(int $itemId): OrderItemRegistryInterface
    {
        $registry = $this->createNewRegistry();
        $this->_resource->load($registry, $itemId, OrderItemRegistry::QUOTE_ITEM_ID);

        if (!$registry->getRegistryId()) {
            throw new NoSuchEntityException(
                __('There is no active registry for quote item %1.', $itemId)
            );
        }

        return $registry;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getByOrderItemId(int $itemId): OrderItemRegistryInterface
    {
        $registry = $this->createNewRegistry();
        $this->_resource->load($registry, $itemId, OrderItemRegistry::ORDER_ITEM_ID);

        if (!$registry->getRegistryId()) {
            throw new NoSuchEntityException(
                __('There is no active registry for order item %1.', $itemId)
            );
        }

        return $registry;
    }

    /**
     * Create a new registry item
     * @return OrderItemRegistry
     */
    private function createNewRegistry(): OrderItemRegistryInterface
    {
        return $this->_modelFactory->create();
    }

    /**
     * Validate seller info
     * @throws ValidationException
     */
    private function validateSellerInfo(array $sellerInfo): void {
        if (!isset($sellerInfo[MerchantDataInterface::SELLER_ID])) {
            throw new ValidationException(
                __('Missing seller id')
            );
        }

        if (!isset($sellerInfo[MerchantDataInterface::DELIVERY_ID])) {
            throw new ValidationException(
                __('Missing delivery id')
            );
        }

        if (!isset($sellerInfo[MerchantDataInterface::PRICE])) {
            throw new ValidationException(
                __('Missing seller price')
            );
        }
    }

    /**
     * Populate registry with seller data
     * @param OrderItemRegistry $registry
     * @param array $data
     * @return void
     */
    private function populateRegistryWithSellerData(
        OrderItemRegistryInterface $registry,
        array $data
    ): void {
        $registry->setItemSellerId((int)$data[MerchantDataInterface::SELLER_ID])
            ->setEstimatedDeliveryId((int)$data[MerchantDataInterface::DELIVERY_ID])
            ->setSellerPrice((float)$data[MerchantDataInterface::PRICE]);

        if (isset($data[MerchantDataInterface::SPECIAL_PRICE])) {
            $registry->setSellerSpecialPrice((float)$data[MerchantDataInterface::SPECIAL_PRICE]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getRegistriesByOrderId(int $orderId): array
    {
        /** @var  $registryCollection RegistryCollection */
        $registryCollection = $this->_collectionFactory->create();
        $registryCollection->addFieldToFilter(OrderItemRegistryInterface::PARENT_ORDER_ID, $orderId);
        return $registryCollection->getItems();
    }
}
