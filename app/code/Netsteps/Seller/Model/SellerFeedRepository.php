<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerFeedInterface;
use Netsteps\Seller\Api\Data\SellerFeedInterfaceFactory;
use Netsteps\Seller\Api\Data\SellerFeedSearchResultsInterfaceFactory;
use Netsteps\Seller\Api\SellerFeedRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\SellerFeed\CollectionFactory;
use Netsteps\Seller\Model\ResourceModel\SellerFeed as ResourceModel;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class SellerFeedRepository implements SellerFeedRepositoryInterface
{

    private ResourceModel $resourceModel;

    protected SellerFeedInterfaceFactory $sellerFeedFactory;

    private LoggerInterface $logger;

    private CollectionFactory $collectionFactory;

    protected SellerFeedSearchResultsInterfaceFactory $searchResultFactory;

    protected CollectionProcessorInterface $collectionProcessor;

    protected array $cached = [];

    /**
     * @param ResourceModel $resourceModel
     * @param SellerFeedInterfaceFactory $sellerFeedFactory
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param SellerFeedSearchResultsInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceModel                       $resourceModel,
        SellerFeedInterfaceFactory          $sellerFeedFactory,
        LoggerInterface                     $logger,
        CollectionFactory                   $collectionFactory,
        SellerFeedSearchResultsInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface        $collectionProcessor
    )
    {
        $this->resourceModel = $resourceModel;
        $this->sellerFeedFactory = $sellerFeedFactory;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): SellerFeedInterface
    {
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Seller Feed with ID "%1"', $id));
        }
        //TODO implement fulldata
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria): \Netsteps\Seller\Api\Data\SellerFeedSearchResultsInterface
    {
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function getBySellerId(int $sellerId): array
    {
        /** @var \Netsteps\Seller\Model\ResourceModel\SellerOption\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SellerFeedInterface::SELLER_ID, $sellerId);
        return $collection->getItems() ?? [];
    }

    /**
     * @inheritDoc
     */
    public function save(SellerFeedInterface $seller): SellerFeedInterface
    {
        $this->resourceModel->save($seller);
        if ($seller->getEntityId()) {
            try {
                $seller = $this->getById($seller->getEntityId());
            } catch (NoSuchEntityException $e) {
            }
        }
        return $seller;
    }

    /**
     * @inheritDoc
     */
    public function delete(SellerFeedInterface $seller): bool
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
    public function createInstance(array $data = [])
    {
        $object = $this->sellerFeedFactory->create();
        $object->addData($data);
        return $object;
    }

}
