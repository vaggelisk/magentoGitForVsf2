<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2023 Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Dir\Reader;

class SeasonValuesSource implements OptionSourceInterface
{

    private string $schemaFile = 'master.xsd';

    private ?string $schema = null;

    private Reader $_reader;

    /**
     * @param Reader $_reader
     * @param string|null $schema
     */
    public function __construct(
        Reader    $_reader,
    )
    {
        $this->_reader = $_reader;
        $this->schema = $_reader->getModuleDir('etc', 'Netsteps_Marketplace') . '/' . $this->schemaFile;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];
        $values = $this->getSeasonValuesFromXSD();
        foreach ($values as $value){
            $result[] = [
                'label' => $value,
                'value' => $value
            ];
        }
        return $result;
    }

    /**
     * @return array
     */
    private function getSeasonValuesFromXSD(): array
    {
        $data = [];
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->load($this->schema);

        $xsdPath = new \DOMXPath($document);
        $nodes = $xsdPath->query('//xs:simpleType[@name="seasonType"]//xs:restriction[@base="xs:string"]')->item(0);

        foreach ($nodes->childNodes as $node) {
            $data[] = $node->getAttribute('value');
        }

        return $data;
    }
}
