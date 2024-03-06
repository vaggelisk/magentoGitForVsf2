<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Plugin\Block\Adminhtml\Integration\Edit;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Seller\Api\SellerIntegrationRepositoryInterface;
use Netsteps\Seller\Model\Config\SellersOptionsSource;

class Form
{

    private SellersOptionsSource $sellersOptionsSource;

    private SellerIntegrationRepositoryInterface $integrationRepository;

    /**
     * @param SellersOptionsSource $sellersOptionsSource
     */
    public function __construct(
        SellersOptionsSource $sellersOptionsSource,
        SellerIntegrationRepositoryInterface $integrationRepository
    )
    {
        $this->sellersOptionsSource = $sellersOptionsSource;
        $this->integrationRepository = $integrationRepository;
    }

    /**
     * @param \Magento\Integration\Block\Adminhtml\Integration\Edit\Form $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundGetFormHtml(\Magento\Integration\Block\Adminhtml\Integration\Edit\Form $subject, \Closure $proceed)
    {
        $currentSellerId = $this->getCurrentSellerId((int)$subject->getRequest()->getParam('id'));
        $form = $subject->getForm();
        if (is_object($form)) {
            $fieldset = $form->addFieldset('seller_integration_relation', ['legend' => __('Seller')]);
            $fieldset->addField(
                'seller_integration',
                'select',
                [
                    'name' => 'seller_integration',
                    'label' => __('Seller'),
                    'id' => 'seller_integration',
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
     * @param int $integrationId
     * @return int
     */
    private function getCurrentSellerId(int $integrationId):int
    {
        $id = 0;
        try {
            $integration = $this->integrationRepository->getByIntegrationId($integrationId);
            $id = $integration->getSellerId();
        } catch (NoSuchEntityException $e) {
        }

        return $id;
    }
}
