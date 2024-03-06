<?php
/**
 * PostUpload
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Controller\Adminhtml\Feed;
use Netsteps\Marketplace\Controller\Adminhtml\AbstractController;
use Magento\Backend\App\Action\Context;
use Netsteps\Marketplace\Exception\InvalidValueException;
use Netsteps\Marketplace\Model\File\ProcessorPoolInterface as ProcessorPool;
use Netsteps\Seller\Model\Config\FeedTypeOptionsSource;

/**
 * Class PostUpload
 * @package Netsteps\Marketplace\Controller\Adminhtml\Feed
 */
class PostUpload extends AbstractController
{
    /**
     * @var ProcessorPool
     */
    private ProcessorPool $_processorPool;

    /**
     * @param Context $context
     * @param \Netsteps\Marketplace\Model\Adminhtml\Context $adminContext
     * @param ProcessorPool $processorPool
     */
    public function __construct(
        Context $context,
        \Netsteps\Marketplace\Model\Adminhtml\Context $adminContext,
        ProcessorPool $processorPool
    )
    {
        $this->_processorPool = $processorPool;
        parent::__construct($context, $adminContext);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $file = $this->getRequest()->getFiles('feed_file');
            $feedType = $this->getRequest()->getParam('feed_type');
            $fileType = pathinfo($file['name'])['extension'];

            if (!$this->_feedMetadata->isAcceptedType($fileType)) {
                throw new InvalidValueException(
                    __(
                        'File type "%1" is invalid. Acceptable file types are: %2',
                        [$fileType, implode(', ', $this->_feedMetadata->getAcceptedFileTypes())]
                    )
                );
            }

            if (!$feedType) {
                throw new InvalidValueException(__('Feed type is required field'));
            }

            $processor = $this->_processorPool->getOrException($fileType);

            $feed = $this->_feedRepository->createEmptyFeed();

            $sellerId = $this->_sellerManager->getLoggedSellerId();

            if (!$sellerId) {
                throw new InvalidValueException(__('Do not have access for this action. You should be a distributor or a merchant.'));
            }

            $feed->setSellerId($sellerId)
                ->setFileType($fileType)
                ->setFeedType($feedType)
                ->setFeedData($processor->processFile($file['tmp_name']));

            $this->_feedRepository->save($feed);

            $this->messageManager->addSuccessMessage(
                __('Feed saved successfully. It will be applied in the next update.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/upload');
    }
}
