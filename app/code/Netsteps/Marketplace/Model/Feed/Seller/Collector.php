<?php
/**
 * Collector
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Seller;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Netsteps\Logger\Model\Logger;
use Netsteps\Marketplace\Exception\InvalidValueException;
use Netsteps\Marketplace\Model\Adminhtml\Context as Context;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;
use Netsteps\Marketplace\Api\FeedRepositoryInterface as FeedRepository;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface as FeedMetadata;
use Netsteps\Marketplace\Model\File\ProcessorPoolInterface as ProcessorPool;

/**
 * Class Collector
 * @package Netsteps\Marketplace\Model\Feed\Seller
 */
class Collector implements CollectorInterface
{
    /**
     * @var Logger
     */
    private Logger $_logger;

    /**
     * @var FeedRepository
     */
    private FeedRepository $_feedRepository;

    /**
     * @var FeedMetadata
     */
    private FeedMetadata $_feedMetadata;

    /**
     * @var ProcessorPool
     */
    private ProcessorPool $_processorPool;

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
     * @param SellerRepository $sellerRepository
     * @param ProcessorPool $processorPool
     */
    public function __construct(
        Context          $context,
        SellerRepository $sellerRepository,
        ProcessorPool    $processorPool
    )
    {
        $this->_logger = $context->getLogger();
        $this->_feedRepository = $context->getFeedRepository();
        $this->_feedMetadata = $context->getFeedMetadata();
        $this->_eventManager = $context->getEventManager();
        $this->_sellerRepository = $sellerRepository;
        $this->_processorPool = $processorPool;
    }

    /**
     * @inheritDoc
     */
    public function collect(): void
    {
        $sellers = $this->_sellerRepository->getList()->getItems();

        /** @var  $seller \Netsteps\Seller\Model\Seller */
        foreach ($sellers as $seller) {
            if (!$seller->getStatus()) {
                continue;
            }

            try {
                foreach ($seller->getFeeds() as $feed) {
                    $url = $feed->getUrlPath();
                    $fileType = $this->getFileType($url);
                    $processor = $this->_processorPool->getOrException($fileType);

                    $timestamp = 't=' . time();

                    if (mb_strpos($url, '?') === false) {
                        $url .= "?{$timestamp}";
                    } else {
                        $url .= "&{$timestamp}";
                    }

                    $feedData = $processor->processFile($url);

                    if ($feedData === '') {
                        throw new LocalizedException(
                            __('Can not fetch %1 feed for %2.', [$feed->getType(), $seller->getName()])
                        );
                    }

                    $newFeed = $this->_feedRepository->createEmptyFeed();
                    $newFeed->setSellerId((int)$seller->getEntityId())
                        ->setFeedType($feed->getType())
                        ->setFileType($fileType)
                        ->setFeedData($feedData);

                    $this->_feedRepository->save($newFeed);
                }
            } catch (\Exception $e) {
                $this->_logger->critical(
                    __('Can not fetch seller %1 feeds. Reason %2', [$seller->getName(), $e->getMessage()])
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function collectBySeller(int $sellerId): void
    {
        // TODO: Implement collectBySeller() method.
    }

    /**
     * @param string $path
     * @return string
     * @throws InvalidValueException
     */
    protected function getFileType(string $path): string {
        $fileType = pathinfo($path)['extension'];

        if (!$this->_feedMetadata->isAcceptedType($fileType)) {
            throw new InvalidValueException(
                __(
                    'File type "%1" is invalid. Acceptable file types are: %2',
                    [$fileType, implode(', ', $this->_feedMetadata->getAcceptedFileTypes())]
                )
            );
        }

        return $fileType;
    }
}
