<?php
/**
 * AbstractDynamicRows
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */

namespace Netsteps\Base\Block\Adminhtml\Form\Field;


use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Netsteps\Base\Block\Adminhtml\Form\Field\Renderer\GenericSelect;
use Magento\Framework\Option\ArrayInterface;

class AbstractDynamicRows extends AbstractFieldArray
{
    const DEFAULT_COLUMNS = [
        'label' => [
            'label' => 'Label',
            'class' => 'required-entry'
        ]
    ];

    private $_renderers = [];

    private $_sources = [];

    /**
     * Prepare columns
     */
    protected function _prepareToRender()
    {
        foreach ($this->getColumnsConfig() as $code => $columnConfig) {
            $this->addColumn($code, $columnConfig);
        }

        $buttonAddText = $this->hasData('addButtonText') ? $this->getData('addButtonText') : __('Add');

        $this->_addAfter = false;
        $this->_addButtonLabel = $buttonAddText;
    }

    /**
     * Get column configuration
     *
     * @return array
     * @throws LocalizedException
     */
    protected function getColumnsConfig()
    {
        if (!$this->hasData('columns') || !is_array($this->getData('columns')) || empty($this->getData('columns'))) {
            $this->setData('columns', self::DEFAULT_COLUMNS);
        }

        $columns = $this->getData('columns');

        foreach ($columns as $code => &$column) {
            if (isset($column['type']) && $column['type'] === 'select') {
                $rendererSource = @$column['rendererSource'];

                if (!$rendererSource) {
                    throw new LocalizedException(
                        __('Column data "rendererSource" is required for type select. Column code %1.', $code)
                    );
                }

                $column['renderer'] = $this->getRenderer($code, $rendererSource);
                unset($column['rendererSource']);
            }
        }

        return $columns;
    }

    /**
     * Get renderer
     *
     * @param $code
     * @param $sourceClass
     * @return GenericSelect
     * @throws LocalizedException
     */
    private function getRenderer($code, $sourceClass)
    {
        if (!array_key_exists($code, $this->_renderers)) {
            /** @var  $renderer GenericSelect */
            $renderer = $this->getLayout()->createBlock(
                GenericSelect::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $source = $this->getSource($sourceClass);
            $renderer->setOptions($source->toOptionArray());
            $this->_renderers[$code] = $renderer;
        }

        return $this->_renderers[$code];
    }

    /**
     * Get option source base on class
     *
     * @param $sourceClass
     * @return OptionSourceInterface
     * @throws LocalizedException
     */
    protected function getSource($sourceClass)
    {
        if (!array_key_exists($sourceClass, $this->_sources)) {
            $this->_sources[$sourceClass] = ObjectManager::getInstance()->create($sourceClass);
        }

        $source = $this->_sources[$sourceClass];
        if (!is_object($source)) {
            throw new LocalizedException(
                __('Source class %1 is not an object', $sourceClass)
            );
        }

        if (!($source instanceof OptionSourceInterface) && !($source instanceof ArrayInterface)){
            throw new LocalizedException(
                __('Source %1 must implements Magento\Framework\Data\OptionSourceInterface
                or Magento\Framework\Option\ArrayInterface', get_class($source))
            );
        }

        return $source;
    }

    /**
     * Prepare rows for select inputs
     *
     * @param \Magento\Framework\DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $options = [];

        $selectCodes = $this->getSelectColumns();

        foreach ($selectCodes as $selectCode => $selectSource){
            $value = $row->getData($selectCode);

            if ($value !== null) {
                $key = $this->getRenderer($selectCode, $selectSource)->calcOptionHash($value);
                $options['option_' . $key] = 'selected="selected"';
            }
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Get select column codes
     *
     * @return array
     */
    private function getSelectColumns() {
        if ($this->hasData('select_column_codes')){
            return $this->getData('select_column_codes');
        }

        $codes = [];

        foreach ($this->getData('columns') as $code => $column){
            if(isset($column['type']) && $column['type'] === 'select'){
                $codes[$code] = $column['rendererSource'];
            }
        }

        $this->setData('select_column_codes', $codes);

        return $this->getData('select_column_codes');
    }
}
