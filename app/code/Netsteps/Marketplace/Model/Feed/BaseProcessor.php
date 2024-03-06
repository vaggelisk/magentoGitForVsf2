<?php
/**
 * AbstractProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Event\Manager;
use Netsteps\Marketplace\Api\Data\FeedInterface;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface;
use Netsteps\Marketplace\Model\Feed\CompositeActionProcessorInterface as CompositeActionProcessor;
use Netsteps\Marketplace\Api\FeedRepositoryInterface as FeedRepository;
use Magento\Framework\Api\SearchCriteriaBuilder as CriteriaBuilder;
use Magento\Framework\Api\FilterBuilder as FilterBuilder;
use Magento\Framework\Api\SortOrderBuilder as OrderBuilder;

/**
 * Class AbstractProcessor
 * @package Netsteps\Marketplace\Model\Feed
 */
class BaseProcessor implements ProcessorInterface
{
    /**
     * @var CompositeActionProcessorInterface
     */
    private CompositeActionProcessorInterface $_feedActionProcessor;

    /**
     * @var FeedRepository
     */
    private FeedRepository $_feedRepository;

    /**
     * @var CriteriaBuilder
     */
    private CriteriaBuilder $_criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private FilterBuilder $_filterBuilder;

    /**
     * @var OrderBuilder
     */
    private OrderBuilder $_orderBuilder;

    /**
     * @var array
     */
    private array $_filters;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected Manager $_eventManager;

    /**
     * @param CompositeActionProcessorInterface $actionProcessor
     * @param FeedRepository $feedRepository
     * @param CriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param OrderBuilder $orderBuilder
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param array $filters
     */
    public function __construct(
        CompositeActionProcessor $actionProcessor,
        FeedRepository $feedRepository,
        CriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        OrderBuilder $orderBuilder,
        Manager $eventManager,
        array $filters = []
    )
    {
        $this->_feedActionProcessor = $actionProcessor;
        $this->_feedRepository = $feedRepository;
        $this->_criteriaBuilder = $criteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->_orderBuilder = $orderBuilder;
        $this->_eventManager = $eventManager;
        $this->_filters = $filters;
    }

    /**
     * @inheritDoc
     */
    public function processFull(): void
    {
        $this->processList([]);
    }

    /**
     * @inheritDoc
     */
    public function processList(array $ids): void
    {
        $criteria = $this->getCriteria($ids);

        /** @var  $feeds FeedInterface[] */
        $feeds = $this->_feedRepository->getList($criteria)->getItems();

        foreach ($feeds as $feed){
            $this->_feedActionProcessor->process($feed);
        }

        if(!empty($feeds)){
            $this->_eventManager->dispatch("marketplace_feed_import_after");
        }
    }

    /**
     * @inheritDoc
     */
    public function processOne(int $id): void
    {
        $feed = $this->_feedRepository->get($id);
        $this->_feedActionProcessor->process($feed);
    }

    /**
     * Get criteria
     * @param int[] $ids
     * @return SearchCriteriaInterface
     */
    protected function getCriteria(array $ids = []): SearchCriteriaInterface {
        $this->_criteriaBuilder->addFilter(
            FeedInterface::STATUS,
            FeedMetadataInterface::STATUS_PENDING
        );

        foreach ($this->_filters as $filterConfig) {
            if(!$this->isValidFilterConfig($filterConfig)) {
                continue;
            }

            $this->_criteriaBuilder->addFilter(
                $filterConfig['field'],
                $filterConfig['value'],
                @$filterConfig['condition'] ?? 'eq'
            );
        }

        if (!empty($ids)){
            $this->_criteriaBuilder->addFilter(
                FeedInterface::ID,
                $ids,
                'in'
            );
        }

        $this->_criteriaBuilder->addSortOrder(
            $this->_orderBuilder->setField(FeedInterface::ID)->setAscendingDirection()->create()
        );

        return $this->_criteriaBuilder->create();
    }

    /**
     * Check if filter config is valid
     * @param array $filter
     * @return bool
     */
    private function isValidFilterConfig(array $filter): bool {
        if (!isset($filter['field'])){
            return false;
        }

        if (!isset($filter['value'])){
            return false;
        }

        return true;
    }
}
