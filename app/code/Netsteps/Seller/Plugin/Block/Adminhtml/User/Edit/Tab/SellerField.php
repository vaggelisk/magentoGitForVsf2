<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Plugin\Block\Adminhtml\User\Edit\Tab;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\SellerAdminRepositoryInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;
use Netsteps\Seller\Model\Config\SellersOptionsSource;

class SellerField
{

    private SellersOptionsSource $sellersOptionsSource;

    private SellerAdminRepositoryInterface $sellerAdminRepository;

    private SellerRepositoryInterface $sellerRepository;

    /**
     * @param SellersOptionsSource $sellersOptionsSource
     * @param SellerAdminRepositoryInterface $sellerAdminRepository
     * @param SellerRepositoryInterface $sellerRepository
     */
    public function __construct(
        SellersOptionsSource $sellersOptionsSource,
        SellerAdminRepositoryInterface $sellerAdminRepository,
        SellerRepositoryInterface $sellerRepository
    )
    {
        $this->sellersOptionsSource = $sellersOptionsSource;
        $this->sellerAdminRepository = $sellerAdminRepository;
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * @param \Magento\User\Block\User\Edit\Tab\Main $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundGetFormHtml(\Magento\User\Block\User\Edit\Tab\Main $subject, \Closure $proceed)
    {
        $currentSellerId = $this->getCurrentSellerId((int)$subject->getRequest()->getParam('user_id'));
        $form = $subject->getForm();
        if (is_object($form))
        {
            $fieldset = $form->addFieldset('seller_admin_declaration', ['legend' => __('Seller')]);
            $fieldset->addField(
                'seller_admin',
                'select',
                [
                    'name' => 'seller_admin',
                    'label' => __('Seller'),
                    'id' => 'seller_admin',
                    'title' => __('Seller'),
                    'value' => $currentSellerId,
                    'values' => $this->sellersOptionsSource->toOptionArray(),
                    'required' => false
                ]
            );
            $subject->setForm($form);
        }

        return $proceed();
    }

    /**
     * @param int $userId
     * @return int
     */
    private function getCurrentSellerId(int $userId):int
    {
        $id = 0;
        try {
            $seller = $this->sellerAdminRepository->getSellerByUserId($userId);
            $id = $seller->getEntityId();
        } catch (NoSuchEntityException $e) {
        }

        return $id;
    }
}
