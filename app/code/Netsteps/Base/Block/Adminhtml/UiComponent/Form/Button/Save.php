<?php
/**
 * Save
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */

namespace Netsteps\Base\Block\Adminhtml\UiComponent\Form\Button;


class Save extends AbstractButton
{
    protected $_label = 'Save';

    protected $_htmlClass = 'save primary';

    protected $_sortOrder = 20;

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
                    'event' => 'save'
                ]
            ],
        ];


        return $buttonData;
    }
}
