<?php
/**
 * Xml
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\File\Processor;


use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Xml\Parser as XmlParser;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterfaceFactory as ItemFactory;

/**
 * Class Xml
 * @package Netsteps\Marketplace\Model\File\Processor
 */
class Xml extends AbstractProcessor
{
    /**
     * @var XmlParser
     */
    private XmlParser $_xmlParser;

    /**
     * @param ItemFactory $itemFactory
     * @param ArrayManager $arrayManager
     * @param XmlParser $parser
     */
    public function __construct(
        ItemFactory  $itemFactory,
        ArrayManager $arrayManager,
        XmlParser    $parser
    )
    {
        $this->_xmlParser = $parser;
        parent::__construct($itemFactory, $arrayManager);
    }


    /**
     * @inheritDoc
     */
    public function processFile(string $filePath): string
    {
        $this->_xmlParser->load($filePath);
        return (string)$this->_xmlParser->getDom()->saveXML();
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processItemData(string $rawData, ?string $path = null): array
    {
        $rawDataArray = $this->processDataAsArray($rawData);
        $items = [];
        $productsData = $path ? $this->_arrayManager->get($path, $rawDataArray) : $rawDataArray;

        //arrayManager can return just the <product> object
        //instead of a listed array
        if(array_is_associative($productsData)){
            $productsData = [$productsData];
        }

        foreach ($productsData as $productsDataRaw){
            $item = $this->createItem();
            $item->addData($productsDataRaw);
            $this->validateVariations($item, $productsDataRaw);
            $items[$productsDataRaw[ItemInterface::SKU]] = $item;
        }

        return $items;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processDataAsArray(string $rawData): array
    {
        $this->_xmlParser->loadXML($rawData);
        return $this->_xmlParser->xmlToArray();
    }


    /**
     * @inheritDoc
     */
    protected function applyVariations(ItemInterface $item, array $rawData): void
    {
        $variationsData = $this->_arrayManager->get('variations/variation', $rawData);

        if (!$variationsData){
            return;
        }

        $variations = [];

        if ($this->isSingleVariation($variationsData)) {
            $variations[] = $this->createVariation()->setData($variationsData);
        } else {
            foreach ($variationsData as $variationData){
                $variations[] = $this->createVariation()->setData($variationData);
            }
        }

        $item->setVariations($variations);
    }

    /**
     * Check if is single variation
     * @param array $data
     * @return bool
     */
    private function isSingleVariation(array $data): bool {
        return !array_key_exists(0, $data);
    }
}
