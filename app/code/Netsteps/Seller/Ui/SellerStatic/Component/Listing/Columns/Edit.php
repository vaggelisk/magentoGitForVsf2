<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Ui\SellerStatic\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Edit extends Column
{
    protected UrlInterface $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface       $urlBuilder,
        array              $components = [],
        array              $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'sellers/sellerstatic/edit',
                        ['entity_id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'ariaLabel' => __('Edit ') . $item['seller_id'],
                    'label' => __('Edit'),
                    'hidden' => false,
                ];

                $item[$this->getData('name')]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'sellers/sellerstatic/delete',
                        ['entity_id' => $item['entity_id']]
                    ),
                    'ariaLabel' => __('Delete ') . $item['seller_id'],
                    'label' => __('Delete'),
                    'hidden' => false,
                    'confirm' => [
                        'title'   => __( 'Delete ' . $item['seller_id']),
                        'message' => __( 'Are you sure you wan\'t to delete this?' )
                    ]
                ];
            }
        }
        return $dataSource;
    }

}
