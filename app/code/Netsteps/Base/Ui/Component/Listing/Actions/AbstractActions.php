<?php
/**
 * AbstractActions
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */

namespace Netsteps\Base\Ui\Component\Listing\Actions;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

abstract class AbstractActions extends Column
{
    /**
     * Primary key field name
     *
     * @var string
     */
    protected $_idFieldName;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * AbstractActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $url
     * @param RequestInterface $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $url,
        RequestInterface $request,
        array $components = [],
        array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_url = $url;
        $this->_request = $request;
    }

    /**
     * Add actions
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[$this->_idFieldName])) {
                    $item[$name]['edit'] = $this->getEditButtonData($item);
                    $item[$name]['delete'] = $this->getDeleteButtonData($item);
                }
            }
        }
        return $dataSource;
    }

    /**
     * Get edit button data
     *
     * @param array $item
     * @return array
     */
    private function getEditButtonData(array $item)
    {
        return [
            'href' => $this->_url->getUrl($this->getEditUrlPath(),
                [
                    'id' => $item[$this->_idFieldName],
                ]),
            'label' => __('Edit')
        ];
    }

    /**
     * Get edit button data
     *
     * @param array $item
     * @return array
     */
    private function getDeleteButtonData(array $item)
    {
        return [
            'href' => $this->_url->getUrl($this->getDeleteUrlPath(), ['id' => $item[$this->_idFieldName]]),
            'label' => __('Delete'),
            'confirm' => $this->getDeleteConfirmationData($item)
        ];;
    }

    /**
     * Get deletion confirmation data array
     * hint: return associated array with keys ['title' => 'Title of deletion', 'message' => 'Confirmation message']
     *
     * @param array $item
     * @return array
     */
    protected function getDeleteConfirmationData(array $item){
        return [
            'title' => __('Delete this item ?'),
            'message' => __('Are you sure you want to delete the item with id %1 ?', $item[$this->_idFieldName])
        ];
    }

    /**
     * Get path for editing entity
     *
     * @return string
     */
    abstract protected function getEditUrlPath();

    /**
     * Get path for delete entity
     *
     * @return mixed
     */
    abstract protected function getDeleteUrlPath();
}
