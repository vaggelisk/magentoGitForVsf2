<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerIntegrationInterface;
use Netsteps\Seller\Api\Data\SellerIntegrationInterfaceFactory;
use Netsteps\Seller\Api\Data\SellerIntegrationSearchResultsInterface;
use Netsteps\Seller\Api\Data\SellerIntegrationSearchResultsInterfaceFactory;
use Netsteps\Seller\Api\SellerIntegrationRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\SellerIntegration as ResourceModel;
use Netsteps\Seller\Model\ResourceModel\SellerIntegration\CollectionFactory;
use Psr\Log\LoggerInterface;

class SellerIntegrationRepository implements SellerIntegrationRepositoryInterface
{

    private ResourceModel $resourceModel;

    protected SellerIntegrationInterfaceFactory $sellerIntegrationInterfaceFactory;

    private CollectionFactory $collectionFactory;

    private CollectionProcessorInterface $collectionProcessor;

    private SellerIntegrationSearchResultsInterfaceFactory $searchResultFactory;

    private LoggerInterface $logger;

    /**
     * @param ResourceModel $resourceModel
     * @param SellerIntegrationInterfaceFactory $sellerIntegrationInterfaceFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceModel                                  $resourceModel,
        SellerIntegrationInterfaceFactory              $sellerIntegrationInterfaceFactory,
        CollectionFactory                              $collectionFactory,
        CollectionProcessorInterface                   $collectionProcessor,
        SellerIntegrationSearchResultsInterfaceFactory $searchResultFactory,
        LoggerInterface                                $logger
    )
    {
        $this->resourceModel = $resourceModel;
        $this->sellerIntegrationInterfaceFactory = $sellerIntegrationInterfaceFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): \Netsteps\Seller\Api\Data\SellerIntegrationInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Seller Integration Relation with ID "%1"', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getBySellerId(int $id): \Netsteps\Seller\Api\Data\SellerIntegrationInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id, SellerIntegrationInterface::SELLER_ID);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Seller Integration Relation with Seller ID "%1"', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getByIntegrationId(int $id): \Netsteps\Seller\Api\Data\SellerIntegrationInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id, SellerIntegrationInterface::INTEGRATION_ID);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Seller Integration Relation with Integration ID "%1"', $id));
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function save(\Netsteps\Seller\Api\Data\SellerIntegrationInterface $object): \Netsteps\Seller\Api\Data\SellerIntegrationInterface
    {
        $this->resourceModel->save($object);
        if ($object->getEntityId()) {
            $object = $this->getById($object->getEntityId());
        }
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null): \Netsteps\Seller\Api\Data\SellerIntegrationSearchResultsInterface
    {
        $searchResults = $this->searchResultFactory->create();
        $collection = $this->collectionFactory->create();

        if ($searchCriteria) {
            $this->collectionProcessor->process($searchCriteria, $collection);
            $searchResults->setSearchCriteria($searchCriteria);
        }

        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerIntegrationInterface $object): bool
    {
        try {
            $this->resourceModel->delete($object);
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
        $object = $this->sellerIntegrationInterfaceFactory->create();
        $object->addData($data);
        return $object;
    }

}
