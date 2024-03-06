<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Plugin\AdminHtml\Controller\Integration;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerIntegrationInterface;
use Netsteps\Seller\Api\Data\SellerIntegrationInterfaceFactory;
use Netsteps\Seller\Api\SellerIntegrationRepositoryInterface;
use Psr\Log\LoggerInterface;

class Save
{

    private SellerIntegrationRepositoryInterface $integrationRepository;

    private SellerIntegrationInterfaceFactory $sellerIntegrationInterfaceFactory;

    private LoggerInterface $logger;

    /**
     * @param SellerIntegrationRepositoryInterface $integrationRepository
     * @param SellerIntegrationInterfaceFactory $sellerIntegrationInterfaceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SellerIntegrationRepositoryInterface $integrationRepository,
        SellerIntegrationInterfaceFactory    $sellerIntegrationInterfaceFactory,
        LoggerInterface                      $logger
    )
    {
        $this->integrationRepository = $integrationRepository;
        $this->sellerIntegrationInterfaceFactory = $sellerIntegrationInterfaceFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\User\Controller\Adminhtml\User\Save $subject
     * @param $result
     * @return mixed
     * @throws AlreadyExistsException
     */
    public function afterExecute(\Magento\Integration\Controller\Adminhtml\Integration\Save $subject, $result)
    {

        $sellerId = (int)$subject->getRequest()->getParam('seller_integration');
        $integrationId = (int)$subject->getRequest()->getParam('id');
        if (!$integrationId) {
            return $result;
        }

        if ($sellerId === 0) {
            $this->deleteRelation($integrationId);
        }

        if ($sellerId > 0) {
            $this->saveRelation($integrationId, $sellerId);
        }

        return $result;
    }

    /**
     * @param int $integrationId
     * @return bool
     */
    private function deleteRelation(int $integrationId): bool
    {
        try {
            $seller = $this->integrationRepository->getByIntegrationId($integrationId);
            try {
                $this->integrationRepository->delete($seller);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        } catch (NoSuchEntityException $e) {
        }

        return true;
    }

    /**
     * @param int $integrationId
     * @param int $sellerId
     * @return void
     * @throws AlreadyExistsException
     */
    private function saveRelation(int $integrationId, int $sellerId): void
    {
        $seller = null;
        try {
            $seller = $this->integrationRepository->getByIntegrationId($integrationId);
            $seller->setIntegrationId($integrationId);
            $seller->setSellerId($sellerId);
        } catch (NoSuchEntityException $e) {

        }

        if (is_null($seller)) {
            $seller = $this->sellerIntegrationInterfaceFactory->create();
            $seller->setSellerId($sellerId);
            $seller->setIntegrationId($integrationId);
        }

        try {
            $this->integrationRepository->save($seller);
        } catch (AlreadyExistsException $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }

    }


}
