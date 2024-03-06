<?php
/**
 * AbstractExporter
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Data;

/**
 * Interface AbstractExporter
 * @package Netsteps\Marketplace\Model\Data
 */
class AbstractExporter implements ExporterInterface
{
    /**
     * @var string[]
     */
    private array $_fields;

    /**
     * @param string[] $fields
     */
    public function __construct(array $fields = [])
    {
        $this->_fields = $fields;
    }

    /**
     * @inheritDoc
     */
    public function export(\Magento\Framework\DataObject $dataObject, array $excluded = []): array
    {
        $fields = $this->_fields;

        foreach ($excluded as $field){
            $index = array_search($field, $fields);

            if ($index !== false){
                unset($fields[$index]);
            }
        }
        return $dataObject->toArray($fields);
    }
}
