<?php
/**
 * GenericSelect
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Primetel
 */

namespace Netsteps\Base\Block\Adminhtml\Form\Field\Renderer;

use Magento\Framework\View\Element\Html\Select;

class GenericSelect extends Select
{
    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }
}
