<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Netsteps\Seller\Api\Data\SellerFeedInterfaceFactory;
use Netsteps\Seller\Api\Data\SellerStaticInterfaceFactory;
use Netsteps\Seller\Api\SellerOptionRepositoryInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;
use Netsteps\Seller\Model\SellerFeedRepository;
use Netsteps\Seller\Model\SellerOptionFactory;
use Netsteps\Seller\Model\SellerStaticRepository;
use Psr\Log\LoggerInterface;
use Netsteps\Seller\Model\ResourceModel\SellerOption\Collection as SellerOptionCollection;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractSeller extends \Magento\Backend\App\Action
{

    public const ADMIN_RESOURCE = 'Netsteps_Seller::edit';

    protected SellerRepositoryInterface $sellerRepository;

    protected SellerOptionRepositoryInterface $sellerOptionRepository;

    protected SellerOptionFactory $sellerOptionFactory;

    protected SellerFeedRepository $sellerFeedRepository;

    protected SellerStaticRepository $sellerStaticRepository;

    protected SellerStaticInterfaceFactory $sellerStaticFactory;

    protected SellerFeedInterfaceFactory $sellerFeedInterfaceFactory;

    protected Registry $registry;

    protected LoggerInterface $logger;

    protected SellerOptionCollection $sellerOptionCollection;

    protected StoreManagerInterface $storeManagerInterface;

    /**
     * @param SellerRepositoryInterface $sellerRepository
     * @param SellerOptionRepositoryInterface $sellerOptionRepository
     * @param SellerOptionFactory $sellerOptionFactory
     * @param SellerFeedRepository $sellerFeedRepository
     * @param SellerFeedInterfaceFactory $sellerFeedInterfaceFactory
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param Context $context
     * @param SellerOptionCollection $context
     */
    public function __construct(
        SellerRepositoryInterface       $sellerRepository,
        SellerOptionRepositoryInterface $sellerOptionRepository,
        SellerOptionFactory             $sellerOptionFactory,
        SellerFeedRepository            $sellerFeedRepository,
        SellerFeedInterfaceFactory      $sellerFeedInterfaceFactory,
        SellerStaticRepository          $sellerStaticRepository,
        SellerStaticInterfaceFactory    $sellerStaticInterfaceFactory,
        Registry                        $registry,
        LoggerInterface                 $logger,
        Context                         $context,
        SellerOptionCollection          $sellerOptionCollection,
        StoreManagerInterface           $storeManagerInterface
    )
    {
        $this->sellerRepository = $sellerRepository;
        $this->sellerOptionRepository = $sellerOptionRepository;
        $this->sellerOptionFactory = $sellerOptionFactory;
        $this->sellerFeedRepository = $sellerFeedRepository;
        $this->sellerFeedInterfaceFactory = $sellerFeedInterfaceFactory;
        $this->sellerStaticRepository = $sellerStaticRepository;
        $this->sellerStaticFactory = $sellerStaticInterfaceFactory;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->sellerOptionCollection = $sellerOptionCollection;
        $this->storeManagerInterface = $storeManagerInterface;
        parent::__construct($context);
    }

}
