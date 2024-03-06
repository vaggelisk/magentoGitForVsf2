<?php
/**
 * CompositeProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Netsteps\Logger\Model\Logger;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface;
use Netsteps\Marketplace\Api\FeedRepositoryInterface;
use Netsteps\Marketplace\Model\Adminhtml\Context;
use Netsteps\Marketplace\Model\File\ProcessorPoolInterface as FileProcessorPool;
use Netsteps\Marketplace\Model\Feed\ActionPoolInterface as ActionPool;
use Netsteps\Marketplace\Traits\Feed\ErrorHandleTrait;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;

/**
 * Class CompositeProcessor
 * @package Netsteps\Marketplace\Model\Feed
 */
class CompositeActionProcessor implements  CompositeActionProcessorInterface
{
    use ErrorHandleTrait;

    /**
     * @var FileProcessorPool
     */
    private FileProcessorPool $_fileProcessorPool;

    /**
     * @var ActionPool
     */
    private ActionPool $_actionPool;

    /**
     * @var FeedRepositoryInterface
     */
    private FeedRepositoryInterface $_feedRepository;

    /**
     * @var Logger
     */
    private Logger $_logger;

    /**
     * @var ArrayManager
     */
    private ArrayManager $_arrayManager;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $_eventManager;

    /**
     * @param Context $context
     * @param FileProcessorPool $processorPool
     * @param ActionPoolInterface $actionPool
     * @param SellerRepository $sellerRepository
     */
    public function __construct(
        Context $context,
        FileProcessorPool $processorPool,
        ActionPool $actionPool,
        SellerRepository $sellerRepository
    )
    {
        $this->_fileProcessorPool = $processorPool;
        $this->_actionPool = $actionPool;
        $this->_sellerRepository = $sellerRepository;
        $this->_logger = $context->getLogger();
        $this->_arrayManager = $context->getArrayManager();
        $this->_feedRepository = $context->getFeedRepository();
        $this->_eventManager = $context->getEventManager();
    }

    /**
     * @inheritDoc
     */
    public function process(\Netsteps\Marketplace\Api\Data\FeedInterface $feed): void
    {
        $hasErrors = false;

        if ($feed->getStatus() !== FeedMetadataInterface::STATUS_PENDING){
            return;
        }

        try {
            $this->_feedRepository->updateStatus($feed, FeedMetadataInterface::STATUS_PROCESSING);
            $seller = $this->_sellerRepository->getById($feed->getSellerId());
            $fileProcessor = $this->_fileProcessorPool->getOrException($feed->getFileType());
            $action = $this->_actionPool->getOrException($feed->getFeedType());
            $errors = $action->validate($seller->getGroup(), $feed, $fileProcessor);
            $action->execute($feed, $fileProcessor);

            $feed->setStatus(FeedMetadataInterface::STATUS_SUCCESS);

            if (count($errors)){
                $hasErrors = true;
                $this->handleValidationErrors($feed, $errors);
            } elseif ($feed->getHasErrors()){
                $hasErrors = true;
            }
        } catch (\Exception $e) {
            $hasErrors = true;
            if ($feed->getStatus() === FeedMetadataInterface::STATUS_PROCESSING){
                $feed->setStatus(FeedMetadataInterface::STATUS_FAILED);
            }
            $this->handleExceptionErrors($feed, $e);
        }

        $feed->setData('updated_at', null);
        $this->_feedRepository->save($feed);

        if ($hasErrors) {
            $this->_eventManager->dispatch('marketplace_feed_process_error', ['feed' => $feed]);
        }

        $this->_eventManager->dispatch("marketplace_feed_process_end", ['feed' => $feed]);
    }
}
