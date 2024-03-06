<?php
/**
 * SaveAndContinue
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */

namespace Netsteps\Base\Block\Adminhtml\UiComponent\Form\Button;


class SaveAndContinue extends AbstractButton
{
    protected $_label = 'Save And Continue Edit';

    protected $_htmlClass = 'save';

    protected $_sortOrder = 10;

    /**
     * @return array
     */
    public function getButtonData()
    {
        $buttonData = parent::getButtonData();

        unset($buttonData['on_click']);

        $buttonData['data_attribute'] = [
            'mage-init' => [
                'button' => [
                    'event' => 'saveAndContinueEdit'
                ]
            ],
        ];


        return $buttonData;
    }
}
