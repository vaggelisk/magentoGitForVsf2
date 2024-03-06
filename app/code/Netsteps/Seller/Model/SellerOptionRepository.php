<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerOptionInterface;
use Netsteps\Seller\Api\Data\SellerOptionInterfaceFactory;
use Netsteps\Seller\Api\SellerOptionRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\SellerOption as ResourceModel;
use Netsteps\Seller\Model\ResourceModel\SellerOption\CollectionFactory;
use Psr\Log\LoggerInterface;

class SellerOptionRepository implements SellerOptionRepositoryInterface
{

    private ResourceModel $resourceModel;

    private SellerOptionInterfaceFactory $sellerOptionInterfaceFactory;

    private LoggerInterface $logger;

    private CollectionFactory $collectionFactory;

    private SellerOptionInterfaceFactory $sellerOptionFactory;

    /**
     * @param ResourceModel $resourceModel
     * @param SellerInterfaceFactory $sellerFactory
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ResourceModel                $resourceModel,
        SellerOptionInterfaceFactory $sellerOptionFactory,
        LoggerInterface              $logger,
        CollectionFactory            $collectionFactory,
    )
    {
        $this->resourceModel = $resourceModel;
        $this->sellerOptionFactory = $sellerOptionFactory;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): ?\Netsteps\Seller\Api\Data\SellerOptionInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Option with ID "%1"', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getBySellerId(int $sellerId, ?int $storeId = null): array
    {
        /** @var \Netsteps\Seller\Model\ResourceModel\SellerOption\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SellerOptionInterface::SELLER_ID, $sellerId);
        return $collection->getItems() ?? [];
    }

    /**
     * @inheritDoc
     */
    public function save(SellerOptionInterface $option): SellerOptionInterface
    {
        $this->resourceModel->save($option);
        if ($option->getEntityId()) {
            $option = $this->getById($option->getEntityId());
        }
        return $option;
    }

    /**
     * @inheritDoc
     */
    public function delete(SellerOptionInterface $option): bool
    {
        try {
            $this->resourceModel->delete($option);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }
        return true;
    }

    /**
     * @param array $data
     * @return SellerOptionInterface
     */
    protected function createInstance(array $data = [])
    {
        $object = $this->sellerOptionFactory->create();
        $object->addData($data);
        return $object;
    }


    /**
     * @inheritDoc
     */
    public function getEmptyOptionModel(): \Netsteps\Seller\Api\Data\SellerOptionInterface
    {
        return $this->createInstance();
    }
}
