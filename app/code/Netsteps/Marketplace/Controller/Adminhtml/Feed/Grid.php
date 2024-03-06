<?php
/**
 * Grid
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Controller\Adminhtml\Feed;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Netsteps\Marketplace\Controller\Adminhtml\AbstractController;

/**
 * Class Grid
 * @package Netsteps\Marketplace\Controller\Adminhtml\Feed
 */
class Grid extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
