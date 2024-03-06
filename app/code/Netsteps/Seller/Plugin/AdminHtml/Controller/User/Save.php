<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Plugin\AdminHtml\Controller\User;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\Data\SellerAdminInterfaceFactory;
use Netsteps\Seller\Api\SellerAdminRepositoryInterface;
use Psr\Log\LoggerInterface;

class Save
{
    private SellerAdminRepositoryInterface $sellerAdminRepository;

    private SellerAdminInterfaceFactory $sellerAdminInterfaceFactory;

    private LoggerInterface $logger;

    /**
     * @param SellerAdminRepositoryInterface $sellerAdminRepository
     * @param SellerAdminInterfaceFactory $sellerAdminInterfaceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SellerAdminRepositoryInterface $sellerAdminRepository,
        SellerAdminInterfaceFactory    $sellerAdminInterfaceFactory,
        LoggerInterface                $logger
    )
    {
        $this->sellerAdminRepository = $sellerAdminRepository;
        $this->sellerAdminInterfaceFactory = $sellerAdminInterfaceFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\User\Controller\Adminhtml\User\Save $subject
     * @param $result
     * @return mixed
     * @throws AlreadyExistsException
     */
    public function afterExecute(\Magento\User\Controller\Adminhtml\User\Save $subject, $result)
    {
        $sellerId = (int)$subject->getRequest()->getParam('seller_admin');
        $userId = (int)$subject->getRequest()->getParam('user_id');

        if (!$userId) {
            return $result;
        }

        if ($sellerId === 0) {
            $this->deleteRelation($userId);
        }

        if ($sellerId > 0) {
            $this->saveRelation($userId, $sellerId);
        }

        return $result;
    }

    /**
     * @param int $userId
     * @return bool
     */
    private function deleteRelation(int $userId): bool
    {
        try {
            $seller = $this->sellerAdminRepository->getByUserId($userId);
            try {
                $this->sellerAdminRepository->delete($seller);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        } catch (NoSuchEntityException $e) {
        }

        return true;
    }

    /**
     * @param int $userId
     * @param int $sellerId
     * @return void
     * @throws AlreadyExistsException
     */
    private function saveRelation(int $userId, int $sellerId): void
    {
        $seller = null;
        try {
            $seller = $this->sellerAdminRepository->getByUserId($userId);
            $seller->setAdminUserId($userId);
            $seller->setSellerId($sellerId);
        } catch (NoSuchEntityException $e) {

        }

        if (is_null($seller)) {
            $seller = $this->sellerAdminInterfaceFactory->create();
            $seller->setSellerId($sellerId);
            $seller->setAdminUserId($userId);
        }

        try {
            $this->sellerAdminRepository->save($seller);
        } catch (AlreadyExistsException $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }

    }


}
