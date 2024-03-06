<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\SaveProcessor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\SellerFeedRepositoryInterface;
use Netsteps\Seller\Model\Seller\SellerProcessorInterface;

class SaveFeeds implements SellerProcessorInterface
{
    private SellerFeedRepositoryInterface $sellerFeedRepository;

    private StoreManagerInterface $storeManager;

    public function __construct(
        SellerFeedRepositoryInterface $sellerFeedRepository,
        StoreManagerInterface $storeManager
    )
    {
        $this->sellerFeedRepository = $sellerFeedRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(SellerInterface $seller): void
    {
        if ($seller->getFeeds()) {
            foreach ($seller->getFeeds() as $feed)
            {
                $feed->setSellerId($seller->getEntityId());
                try {
                    $feed->setStoreId($this->storeManager->getStore()->getId());
                } catch (NoSuchEntityException $e) {
                }
                $this->sellerFeedRepository->save($feed);
            }
        }
    }
}
