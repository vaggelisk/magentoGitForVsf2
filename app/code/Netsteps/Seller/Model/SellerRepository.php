<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\Data\SellerInterfaceFactory;
use Netsteps\Seller\Api\Data\SellerSearchResultsInterfaceFactory;
use Netsteps\Seller\Api\SellerRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\Seller\CollectionFactory;
use Netsteps\Seller\Model\ResourceModel\Seller as ResourceModel;
use Netsteps\Seller\Model\Seller\SellerProcessorInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class SellerRepository implements SellerRepositoryInterface
{
    /**
     * @var SellerInterface[]
     */
    private array $_instances = [];

    private ResourceModel $resourceModel;

    protected SellerInterfaceFactory $sellerFactory;

    private LoggerInterface $logger;

    private CollectionFactory $collectionFactory;

    protected SellerSearchResultsInterfaceFactory $searchResultFactory;

    protected CollectionProcessorInterface $collectionProcessor;

    private array $loadProcessors;

    protected array $cached = [];

    /**
     * @param ResourceModel $resourceModel
     * @param SellerInterfaceFactory $sellerFactory
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param SellerSearchResultsInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param array $loadProcessors
     */
    public function __construct(
        ResourceModel                       $resourceModel,
        SellerInterfaceFactory              $sellerFactory,
        LoggerInterface                     $logger,
        CollectionFactory                   $collectionFactory,
        SellerSearchResultsInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface        $collectionProcessor,
        array $loadProcessors = []
    )
    {
        $this->resourceModel = $resourceModel;
        $this->sellerFactory = $sellerFactory;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->loadProcessors = $loadProcessors;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id, bool $fullData = false): \Netsteps\Seller\Api\Data\SellerInterface
    {
        if (array_key_exists($id, $this->_instances)){
            return $this->_instances[$id];

        }
        $object = $this->createInstance();
        $this->resourceModel->load($object, $id);
        if (!$object->getEntityId()) {
            throw new NoSuchEntityException(__('Unable to find Seller with ID "%1"', $id));
        }
        //TODO implement fulldata

        $this->_instances[$object->getEntityId()] = $object;
        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null): \Netsteps\Seller\Api\Data\SellerSearchResultsInterface
    {
        $searchResults = $this->searchResultFactory->create();

        $collection = $this->collectionFactory->create();

        if ($searchCriteria){
            $this->collectionProcessor->process($searchCriteria, $collection);
            $searchResults->setSearchCriteria($searchCriteria);
        }

        $searchResults->setTotalCount($collection->getSize());

        foreach ($collection->getItems() as $item)
        {
            /** @var SellerProcessorInterface $processor */
            foreach ($this->loadProcessors as $processor){
                $processor->execute($item);
            }
        }

        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(\Netsteps\Seller\Api\Data\SellerInterface $seller): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->resourceModel->save($seller);
        if ($seller->getEntityId()) {
            if (array_key_exists($seller->getEntityId(), $this->_instances)){
                unset($this->_instances[$seller->getEntityId()]);
            }

            $seller = $this->getById($seller->getEntityId());
        }
        return $seller;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Netsteps\Seller\Api\Data\SellerInterface $seller): bool
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
    protected function createInstance(array $data = []): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $object = $this->sellerFactory->create();
        $object->addData($data);
        return $object;
    }


    /**
     * @inheritDoc
     */
    public function getEmptySellerModel(): \Netsteps\Seller\Api\Data\SellerInterface
    {
        return $this->createInstance();
    }
}
