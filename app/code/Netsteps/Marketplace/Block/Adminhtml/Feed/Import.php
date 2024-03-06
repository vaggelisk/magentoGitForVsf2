<?php
/**
 * Import
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Block\Adminhtml\Feed;

/**
 * Class Import
 * @package Netsteps\Marketplace\Block\Adminhtml\Feed
 */
class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Setup form container basic information
     * @return void
     */
    protected function _construct()
    {
        $this->_headerText = __('Import Feed');
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_feed';
        $this->_blockGroup = 'Netsteps_Marketplace';
        $this->_mode = 'import';
        parent::_construct();
        $this->buttonList->remove('delete');
    }
}
