<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Config;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;

class SellersOptionsSource implements OptionSourceInterface
{
    private SellerRepositoryInterface $sellerRepository;

    /**
     * @param SellerRepositoryInterface $sellerRepository
     */
    public function __construct(SellerRepositoryInterface $sellerRepository)
    {
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray():array
    {
        return $this->getSellersData();
    }

    /**
     * @return array
     */
    private function getSellersData():array
    {
        $data[] = ['value' => 0, 'label' => __('Select Seller')];
        $searchCriteria = ObjectManager::getInstance()->get(\Magento\Framework\Api\SearchCriteriaInterface::class);
        $sellers = $this->sellerRepository->getList($searchCriteria);
        if ($sellers->getTotalCount() > 0) {
            foreach ($sellers->getItems() as $seller) {
                $data[] = [
                    'value' => $seller->getEntityId(),
                    'label' => $seller->getName()
                ];
            }
        }
        return $data;
    }
}
