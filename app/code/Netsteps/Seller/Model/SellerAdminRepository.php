<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data;
use Netsteps\Seller\Api\Data\SellerAdminInterfaceFactory;
use Netsteps\Seller\Api\SellerAdminRepositoryInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\SellerAdmin as ResourceModel;
use Psr\Log\LoggerInterface;

class SellerAdminRepository implements SellerAdminRepositoryInterface
{
    private ResourceModel $resourceModel;

    protected SellerAdminInterfaceFactory $sellerAdminFactory;

    private SellerRepositoryInterface $sellerRepository;

    private LoggerInterface $logger;

    /**
     * @param ResourceModel $resourceModel
     * @param SellerAdminInterfaceFactory $sellerAdminFactory
     * @param SellerRepositoryInterface $sellerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceModel               $resourceModel,
        SellerAdminInterfaceFactory $sellerAdminFactory,
        SellerRepositoryInterface   $sellerRepository,
        LoggerInterface             $logger
    )
    {
        $this->resourceModel = $resourceModel;
        $this->sellerAdminFactory = $sellerAdminFactory;
        $this->sellerRepository = $sellerRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getBySellerId(int $id): \Netsteps\Seller\Api\Data\SellerAdminInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id, Data\SellerAdminInterface::SELLER_ID);
        if (!$object->getSellerId()) {
            throw new NoSuchEntityException(__('Seller ID "%1" is not assigned to an Admin', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getByUserId(int $id): \Netsteps\Seller\Api\Data\SellerAdminInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id, Data\SellerAdminInterface::USER_ID);
        if (!$object->getAdminUserId()) {
            throw new NoSuchEntityException(__('Admin ID "%1" is not assigned to a Seller', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('No relation Seller <- -> Admin with ID "%1" was found', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getSellerByUserId(int $id, $fullData = false): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $object = $this->getByUserId($id);
        return $this->sellerRepository->getById($object->getSellerId(), $fullData);
    }

    /**
     * @inheritDoc
     *
     */
    public function save(\Netsteps\Seller\Api\Data\SellerAdminInterface $data): \Netsteps\Seller\Api\Data\SellerAdminInterface
    {
        $this->resourceModel->save($data);
        if ($data->getSellerId()) {
            $data = $this->getBySellerId($data->getSellerId());
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function delete(Data\SellerAdminInterface $data): bool
    {
        try {
            $this->resourceModel->delete($data);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function createInstance(array $data = [])
    {
        $object = $this->sellerAdminFactory->create();
        $object->addData($data);
        return $object;
    }


}
