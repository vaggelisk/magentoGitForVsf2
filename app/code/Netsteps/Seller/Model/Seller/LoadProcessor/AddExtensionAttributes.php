<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2023 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\LoadProcessor;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\SellerStaticRepositoryInterface;
use Netsteps\Seller\Model\Seller\SellerProcessorInterface;

class AddExtensionAttributes implements SellerProcessorInterface
{
    private SellerStaticRepositoryInterface $repository;

    /**
     * @param SellerStaticRepositoryInterface $repository
     */
    public function __construct(
        SellerStaticRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function execute(SellerInterface $seller): void
    {
        if (!empty($seller->getData())) {
            try {
                $staticData = $this->repository->getBySellerId($seller->getEntityId());
                $extensionAttributes = $seller->getExtensionAttributes();
                $extensionAttributes->setIban($staticData->getIban());
                $extensionAttributes->setBeneficiary($staticData->getBeneficiary());
                $seller->setExtensionAttributes($extensionAttributes);
            } catch (NoSuchEntityException $e) {
            }
        }
    }
}
