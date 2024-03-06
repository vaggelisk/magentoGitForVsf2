<?php
/**
 * AbstractController
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface as Timezone;
use Netsteps\Logger\Model\Logger;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface;
use Netsteps\Marketplace\Api\FeedRepositoryInterface;
use Netsteps\Marketplace\Model\Adminhtml\Context as AdminContext;
use Netsteps\Seller\Model\Admin\SellerManagementInterface;

/**
 * Class AbstractController
 * @package Netsteps\Marketplace\Controller\Adminhtml
 */
abstract class AbstractController extends \Magento\Backend\App\Action
{
    /**
     * @var Logger
     */
    protected Logger $_logger;

    /**
     * @var ArrayManager
     */
    protected ArrayManager $_arrayManager;

    /**
     * @var Session
     */
    protected Session $_adminSession;

    /**
     * @var Registry
     */
    protected Registry $_registry;

    /**
     * @var Timezone
     */
    protected Timezone $_timezone;

    /**
     * @var FeedRepositoryInterface
     */
    protected FeedRepositoryInterface $_feedRepository;

    /**
     * @var FeedMetadataInterface
     */
    protected FeedMetadataInterface $_feedMetadata;

    /**
     * @var SellerManagementInterface
     */
    protected SellerManagementInterface $_sellerManager;

    /**
     * @param Context $context
     * @param AdminContext $adminContext
     */
    public function __construct(
        Context $context,
        AdminContext $adminContext
    )
    {
        $this->_logger = $adminContext->getLogger();
        $this->_arrayManager = $adminContext->getArrayManager();
        $this->_adminSession = $adminContext->getAdminSession();
        $this->_registry = $adminContext->getRegistry();
        $this->_timezone = $adminContext->getTimezone();
        $this->_feedRepository = $adminContext->getFeedRepository();
        $this->_feedMetadata = $adminContext->getFeedMetadata();
        $this->_sellerManager = $adminContext->getSellerManager();
        parent::__construct($context);
    }

    /**
     * Decides if admin is allowed to have access to seller actions
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Netsteps_Marketplace::seller_actions');
    }
}
