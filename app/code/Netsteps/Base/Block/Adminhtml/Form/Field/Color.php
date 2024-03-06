<?php
/**
 * Color
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */
namespace Netsteps\Base\Block\Adminhtml\Form\Field;

/**
 * @package Netsteps\Base\Block\Adminhtml\Form\Field
 */
class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Element set type to color
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setType('color');
        return parent::_getElementHtml($element);
    }
}
