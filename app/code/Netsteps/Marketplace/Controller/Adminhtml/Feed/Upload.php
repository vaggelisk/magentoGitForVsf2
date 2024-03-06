<?php
/**
 * Upload
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Controller\Adminhtml\Feed;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

/**
 * Class Upload
 * @package Netsteps\Marketplace\Controller\Adminhtml\Feed
 */
class Upload extends \Netsteps\Marketplace\Controller\Adminhtml\AbstractController
{

    /**
     * @inheritDoc
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        //TODO remove debug if after create process
        if ($fid = $this->getRequest()->getParam('feed_id')){
            /** @var  $processor \Netsteps\Marketplace\Model\Feed\CompositeActionProcessorInterface */
            $processor = ObjectManager::getInstance()->get(\Netsteps\Marketplace\Model\Feed\CompositeActionProcessorInterface::class);
            $processor->process($this->_feedRepository->get($fid));
        }

        $nameParts = ['Upload Feed'];
        $seller = $this->_sellerManager->getLoggedSeller();

        if ($seller){
            array_unshift($nameParts, $seller->getName());
        }

        /** @var  $page Page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $page->getConfig()->getTitle()->set(implode(' | ', $nameParts));
        return $page;
    }

    /**
     * Check if user allowed to upload file
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Netsteps_Marketplace::action_upload');
    }
}
