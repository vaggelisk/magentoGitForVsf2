<?php
/**
 * Form
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Block\Adminhtml\Feed\Import;

use Netsteps\Seller\Api\Data\SellerGroupInterface;
use Netsteps\Seller\Model\Config\FeedTypeOptionsSource;
use Netsteps\Seller\Model\Admin\SellerManagementInterface as AdminSellerManagement;

/**
 * Class Form
 * @package Netsteps\Marketplace\Block\Adminhtml\Feed\Import
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var FeedTypeOptionsSource
     */
    private FeedTypeOptionsSource $_feedTypeSource;

    /**
     * @var AdminSellerManagement
     */
    private AdminSellerManagement $_sellerManagement;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param FeedTypeOptionsSource $feedTypeOptionsSource
     * @param AdminSellerManagement $sellerManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry             $registry,
        \Magento\Framework\Data\FormFactory     $formFactory,
        FeedTypeOptionsSource                   $feedTypeOptionsSource,
        AdminSellerManagement                   $sellerManagement,
        array                                   $data = [])
    {
        $this->_feedTypeSource = $feedTypeOptionsSource;
        $this->_sellerManagement = $sellerManagement;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     * @return Form|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        //Create main form
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/postUpload'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ]
        ]);

        //Add base fieldset to form
        $baseFieldset = $form->addFieldset('general', ['legend' => __('Import Feed')]);

        //Add file uploader input
        $baseFieldset->addField(
            'feed_file',
            'file',
            [
                'name' => 'feed_file',
                'label' => __('Feed File'),
                'title' => __('Feed File'),
                'notice' => __('Acceptable types are CSV/XML'),
                'required' => true
            ]
        );

        $optionFilterMethod = $this->_sellerManagement->getSellerGroup() === SellerGroupInterface::GROUP_DISTRIBUTOR ?
            'isValidDistributorOption' : 'isValidMerchantOption';

        //Add feed type input
        $baseFieldset->addField(
            'feed_type',
            'select',
            [
                'name' => 'feed_type',
                'label' => __('Feed Type'),
                'title' => __('Feed Type'),
                'required' => true,
                'values' => array_filter($this->_feedTypeSource->toOptionArray(), [$this, $optionFilterMethod])
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check feed type option if is valid for distributor
     * @param array $typeOption
     * @return bool
     */
    private function isValidDistributorOption(array $typeOption): bool
    {
        return $typeOption['value'] !== FeedTypeOptionsSource::TYPE_MERCHANT;
    }

    /**
     * Check feed type option if is valid for merchant
     * @param array $typeOption
     * @return bool
     */
    private function isValidMerchantOption(array $typeOption): bool
    {
        return !in_array(
            $typeOption['value'],
            [
                FeedTypeOptionsSource::TYPE_MASTER,
                FeedTypeOptionsSource::TYPE_LOCALE,
                FeedTypeOptionsSource::TYPE_IMAGES
            ]
        );
    }
}
