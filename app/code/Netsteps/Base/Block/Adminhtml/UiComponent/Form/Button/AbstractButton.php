<?php
/**
 * AbstractButton
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */

namespace Netsteps\Base\Block\Adminhtml\UiComponent\Form\Button;


use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Template;

abstract class AbstractButton extends Template implements ButtonProviderInterface
{
    /**
     * Button label
     *
     * @var string
     */
    protected $_label;

    /**
     * Button html class
     *
     * @var string
     */
    protected $_htmlClass;

    /**
     * Button sort order
     *
     * @var int
     */
    protected $_sortOrder;

    /**
     * Action arguments
     *
     * @var array
     */
    protected $_actionArguments = [];

    /**
     * Action path
     *
     * @var string
     */
    protected $_path;

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __($this->_label),
            'class' => $this->_htmlClass,
            'on_click' => sprintf( "location.href = '%s';", $this->getActionUrl() ),
            'sort_order' => $this->_sortOrder,
        ];
    }

    /**
     * Get default url params as array
     *
     * @return array
     */
    protected function getDefaultUrlParams () {
        return [ '_current' => true, '_query' => [ 'isAjax' => null ] ];
    }

    /**
     * Get base action url
     *
     * @return string
     */
    protected function getActionUrl() {
        $params = array_merge($this->getDefaultUrlParams(), $this->_actionArguments);
        return $this->getUrl($this->_path, $params);
    }
}
