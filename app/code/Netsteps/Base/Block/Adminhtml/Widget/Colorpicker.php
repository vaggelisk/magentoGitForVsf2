<?php
/**
 * Colorpicker
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */
namespace Netsteps\Base\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use \Magento\Backend\Block\Template\Context as TemplateContext;
use \Magento\Framework\Data\Form\Element\Factory as FormElementFactory;

/**
 * Class Colorpicker
 * @package Netsteps\Base\Block\Adminhtml\Widget
 */
class Colorpicker extends Template
{
    /**
     * @var FormElementFactory
     */
    protected $_elementFactory;

    /**
     * Colorpicker constructor.
     * @param TemplateContext $context
     * @param FormElementFactory $elementFactory
     * @param array $data
     */
    public function __construct(TemplateContext $context, FormElementFactory $elementFactory, array $data = [])
    {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Create element new html
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $input = $this->_elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setType('color');
        $input->setClass("widget-option input-text admin__control-text");

        return $element;
    }
}
