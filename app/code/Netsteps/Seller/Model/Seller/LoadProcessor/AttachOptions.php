<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\LoadProcessor;

use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\SellerOptionRepositoryInterface;
use Netsteps\Seller\Model\Seller\SellerProcessorInterface;
use Netsteps\Seller\Model\SellerOption\OptionCodes;

class AttachOptions implements SellerProcessorInterface
{
    private SellerOptionRepositoryInterface $sellerOptionRepository;

    private OptionCodes $optionCodes;

    /**
     * @param SellerOptionRepositoryInterface $sellerOptionRepository
     * @param OptionCodes $optionCodes
     */
    public function __construct(
        SellerOptionRepositoryInterface $sellerOptionRepository,
        OptionCodes $optionCodes
    )
    {
        $this->sellerOptionRepository = $sellerOptionRepository;
        $this->optionCodes = $optionCodes;
    }

    /**
     * @inheritDoc
     */
    public function execute(SellerInterface $seller): void
    {
        if(!empty($seller->getData())){
            $options = $this->sellerOptionRepository->getBySellerId($seller->getEntityId());
            if(empty($options)){
                $options = $this->optionCodes->createEmptyOptionFields();
            }
            $seller->setOptions($options);
        }
    }
}
