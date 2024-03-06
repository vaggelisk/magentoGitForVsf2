<?php
/**
 * FeedRepository
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model;

use Magento\Framework\Exception\NotFoundException;
use Netsteps\Marketplace\Api\Data;
use Netsteps\Marketplace\Api\Data\FeedInterface;
use Netsteps\Marketplace\Api\FeedRepositoryInterface;
use Netsteps\Marketplace\Api\Data\FeedInterfaceFactory as FeedFactory;
use Netsteps\Marketplace\Model\ResourceModel\Feed as ResourceModel;
use Netsteps\Marketplace\Model\ResourceModel\Feed\Collection as FeedCollection;
use Netsteps\Marketplace\Model\ResourceModel\Feed\CollectionFactory as FeedCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface as CollectionProcessor;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

/**
 * Class FeedRepository
 * @package Netsteps\Marketplace\Model
 */
class FeedRepository implements FeedRepositoryInterface
{
    private array $instances = [];

    /**
     * @var FeedFactory
     */
    private FeedFactory $_modelFactory;

    /**
     * @var FeedCollectionFactory
     */
    private FeedCollectionFactory $_collectionFactory;

    /**
     * @var ResourceModel
     */
    private ResourceModel $_resource;

    /**
     * @var CollectionProcessor
     */
    private CollectionProcessor $_collectionProcessor;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private SearchResultsInterfaceFactory $_searchResultFactory;

    /**
     * @param FeedFactory $modelFactory
     * @param FeedCollectionFactory $collectionFactory
     * @param ResourceModel $resource
     * @param CollectionProcessor $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        FeedFactory           $modelFactory,
        FeedCollectionFactory $collectionFactory,
        ResourceModel         $resource,
        CollectionProcessor   $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory
    )
    {
        $this->_modelFactory = $modelFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_resource = $resource;
        $this->_collectionProcessor = $collectionProcessor;
        $this->_searchResultFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save (\Netsteps\Marketplace\Api\Data\FeedInterface $feed): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        $this->_resource->save($feed);
        return $this->get($feed->getFeedId(), true);
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     */
    public function get(int $feedId, bool $force = false): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        if (array_key_exists($feedId, $this->instances) && !$force){
            return $this->instances[$feedId];
        }

        $feed = $this->createEmptyFeed();
        $this->_resource->load($feed, $feedId);

        if (!$feed->getFeedId()){
            throw new NotFoundException(
                __('Feed with id %1 does not exist.', $feedId)
            );
        }

        $this->instances[$feedId] = $feed;

        return $this->instances[$feedId];
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function deleteById(int $feedId): bool
    {
        $feed = $this->get($feedId);
        $this->_resource->delete($feed);

        if (array_key_exists($feedId, $this->instances)){
            unset($this->instances[$feedId]);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null): \Magento\Framework\Api\SearchResultsInterface
    {
        /** @var  $collection FeedCollection */
        $collection = $this->_collectionFactory->create();

        if ($searchCriteria){
            $this->_collectionProcessor->process($searchCriteria, $collection);
        }

        $items = [];
        /** @var  $feed FeedInterface */
        foreach ($collection as $feed) {
            $this->instances[$feed->getFeedId()] = $feed;
            $items[] = $feed;
        }

        /** @var  $searchResult SearchResultsInterface */
        $searchResult = $this->_searchResultFactory->create();
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($items);

        if ($searchCriteria) {
            $searchResult->setSearchCriteria($searchCriteria);
        }

        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function getSellerFeedCollection(int $sellerId): \Netsteps\Marketplace\Model\ResourceModel\Feed\Collection
    {
        /** @var  $feeds FeedCollection */
        $feeds = $this->_collectionFactory->create();
        $feeds->addSellerFilter($sellerId);
        return $feeds;
    }

    /**
     * @inheritDoc
     */
    public function createEmptyFeed(): \Netsteps\Marketplace\Api\Data\FeedInterface
    {
        return $this->_modelFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function updateStatus(\Netsteps\Marketplace\Api\Data\FeedInterface $feed, string $status): void
    {
        if($feed->getStatus() === $status){
            return;
        }

        $feed->setStatus($status);

        $this->_resource->getConnection()
            ->update(
                $this->_resource->getMainTable(),
                [\Netsteps\Marketplace\Api\Data\FeedInterface::STATUS => $status],
                [$this->_resource->getIdFieldName() . ' = ?' => $feed->getFeedId()]
            );
    }
}
