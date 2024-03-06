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
use Netsteps\Seller\Api\SellerOptionRepositoryInterface;
use Netsteps\Seller\Model\Seller\SellerProcessorInterface;

class SaveOptions implements SellerProcessorInterface
{
    private SellerOptionRepositoryInterface $sellerOptionRepository;

    private StoreManagerInterface $storeManager;

    public function __construct(
        SellerOptionRepositoryInterface $sellerOptionRepository,
        StoreManagerInterface $storeManager
    )
    {
        $this->sellerOptionRepository = $sellerOptionRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(SellerInterface $seller): void
    {
        if ($seller->getOptions()) {
            foreach ($seller->getOptions() as $option)
            {
                if($option->getOptionName() === 'logo'){
                    $value = $option->getOptionValue();
                    if(is_array($value) && isset($value[0]['url'])){
                        $option->setOptionValue($value[0]['url']);
                    }else {
                        $option->setOptionValue(null);
                    }
                }
                $option->setSellerId($seller->getEntityId());
                try {
                    $option->setStoreId($this->storeManager->getStore()->getId());
                } catch (NoSuchEntityException $e) {
                }
                $this->sellerOptionRepository->save($option);
            }
        }
    }
}
