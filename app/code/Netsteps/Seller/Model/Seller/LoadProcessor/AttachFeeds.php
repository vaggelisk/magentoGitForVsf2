<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\LoadProcessor;

use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\SellerFeedRepositoryInterface;
use Netsteps\Seller\Model\Seller\SellerProcessorInterface;

class AttachFeeds implements SellerProcessorInterface
{
    private SellerFeedRepositoryInterface $sellerFeedRepository;

    /**
     * @param SellerFeedRepositoryInterface $sellerFeedRepository
     */
    public function __construct(
        SellerFeedRepositoryInterface $sellerFeedRepository,
    )
    {
        $this->sellerFeedRepository = $sellerFeedRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(SellerInterface $seller): void
    {
        if (!empty($seller->getData())) {
            $feeds = $this->sellerFeedRepository->getBySellerId($seller->getEntityId());
            if(!empty($feeds)){
                $seller->setFeeds($feeds);
            }
        }
    }
}
