<?php
/**
 * Colorpicker
 *
 * @copyright Copyright © 2018 Kostas Tsiapalis. All rights reserved.
 * @author    k.tsiapalis86@gmail.com
 */

namespace Netsteps\Base\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class Colorpicker
 * @package Netsteps\Base\Block\Adminhtml\System\Config
 * @deprecated Needs to be swapped with UI way
 */
class Colorpicker extends Field
{
    /**
     * Added script for color picker
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();

        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                	var $el = $("#' . $element->getHtmlId() . '");
                    $el.css("backgroundColor", "'. $value .'");

                    $el.prop("readonly", true);

                    $el.css("opacity", "1");
                	// Attach the color picker
                    $el.ColorPicker({
                    	color: "'. $value .'",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                    	}
                	});
            	});
        	});
	        </script>';
        return $html;
    }
}
