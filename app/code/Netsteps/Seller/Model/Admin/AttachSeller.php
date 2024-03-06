<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Admin;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Model\SellerAdminRepository;
use Magento\Framework\App\Request\DataPersistorInterface;
class AttachSeller
{
    const REGISTRY_KEY = 'seller';

    protected Session $userSession;

    protected SellerAdminRepository $sellerAdminRepository;

    protected DataPersistorInterface $dataPersistor;

    /**
     * @param Session $userSession
     * @param SellerAdminRepository $sellerAdminRepository
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $userSession,
        SellerAdminRepository $sellerAdminRepository,
        DataPersistorInterface $dataPersistor
    )
    {
        $this->userSession = $userSession;
        $this->sellerAdminRepository = $sellerAdminRepository;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute():void
    {
        if(!$this->userSession->isLoggedIn()){
            return;
        }

        try {
            $seller = $this->sellerAdminRepository->getSellerByUserId($this->userSession->getUser()->getId(),true);
            $this->dataPersistor->set(self::REGISTRY_KEY, $seller);
        } catch (NoSuchEntityException $e) {
            $this->dataPersistor->clear(self::REGISTRY_KEY);
        }
    }
}
