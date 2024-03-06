<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerStaticInterface;
use Netsteps\Seller\Api\Data\SellerStaticInterfaceFactory;
use Netsteps\Seller\Api\SellerStaticRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\SellerStatic\CollectionFactory;
use Netsteps\Seller\Model\ResourceModel\SellerStatic as ResourceModel;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class SellerStaticRepository implements SellerStaticRepositoryInterface
{
    private ResourceModel $resourceModel;

    protected SellerStaticInterfaceFactory $sellerStaticFactory;

    private LoggerInterface $logger;

    private CollectionFactory $collectionFactory;

    protected CollectionProcessorInterface $collectionProcessor;

    protected array $cached = [];

    /**
     * @param ResourceModel $resourceModel
     * @param SellerStaticInterfaceFactory $sellerStaticFactory
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceModel                $resourceModel,
        SellerStaticInterfaceFactory $sellerStaticFactory,
        LoggerInterface              $logger,
        CollectionFactory            $collectionFactory,
        CollectionProcessorInterface $collectionProcessor
    )
    {
        $this->resourceModel = $resourceModel;
        $this->sellerStaticFactory = $sellerStaticFactory;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Seller Static Data with ID "%1"', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getBySellerId(int $id): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id, SellerStaticInterface::SELLER_ID);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Seller Static Data with ID "%1"', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function save(\Netsteps\Seller\Api\Data\SellerStaticInterface $seller): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        $this->resourceModel->save($seller);
        if ($seller->getEntityId()) {
            $seller = $this->getById($seller->getEntityId());
        }
        return $seller;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerStaticInterface $seller): bool
    {
        try {
            $this->resourceModel->delete($seller);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        $entity = $this->getById($id);
        return $this->delete($entity);
    }

    /**
     * @inheritDoc
     */
    protected function createInstance(array $data = [])
    {
        $object = $this->sellerStaticFactory->create();
        $object->addData($data);
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getEmptySellerStaticModel(): \Netsteps\Seller\Api\Data\SellerStaticInterface
    {
        return $this->createInstance();
    }
}
