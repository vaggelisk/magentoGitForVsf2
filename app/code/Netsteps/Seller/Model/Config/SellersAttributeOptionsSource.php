<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;

class SellersAttributeOptionsSource extends AbstractSource implements OptionSourceInterface
{

    private SellerRepositoryInterface $sellerRepository;

    private FilterBuilder $filterBuilder;

    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param SellerRepositoryInterface $sellerRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SellerRepositoryInterface $sellerRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->sellerRepository = $sellerRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $data[] = ['value' => '', 'label' => ' '];
        $distributors = $this->getDistributors();
        foreach ($distributors as $distributor){
            $data[] = [
                'value' => $distributor->getEntityId(),
                'label' => $distributor->getName()
            ];
        }
        return $data;
    }

    /**
     * @return \Netsteps\Seller\Api\Data\SellerInterface[]
     */
    private function getDistributors(): array
    {
        $result = [];

        $distributors = $this->sellerRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        if(count ($distributors->getItems()) > 0){
            $result = $distributors->getItems();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
