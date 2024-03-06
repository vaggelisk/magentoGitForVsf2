<?php
/**
 * Csv
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\File\Processor;

use Magento\Framework\File\Csv as CsvParser;
use Magento\Framework\Stdlib\ArrayManager;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterfaceFactory as ItemFactory;

/**
 * Class Csv
 * @package Netsteps\Marketplace\Model\File\Processor
 */
class Csv extends AbstractProcessor
{
    const KEYS = 'keys';
    const DATA = 'data';

    /**
     * @var CsvParser
     */
    protected CsvParser $_csvParser;

    /**
     * @param ItemFactory $itemFactory
     * @param ArrayManager $arrayManager
     * @param CsvParser $csvParser
     */
    public function __construct(
        ItemFactory $itemFactory,
        ArrayManager $arrayManager,
        CsvParser $csvParser
    )
    {
        parent::__construct($itemFactory, $arrayManager);
        $this->_csvParser = $csvParser;
        $this->_construct();
    }

    /**
     * Override this method instead of original __construct
     * @return void
     */
    protected function _construct(): void {

    }

    /**
     * @inheritDoc
     */
    public function processFile(string $filePath): string
    {
        $csvData = $this->_csvParser->getData($filePath);
        $headers = array_shift($csvData);
        $feedData = [self::KEYS => $headers, self::DATA => $csvData];
        return json_encode($feedData);
    }

    /**
     * @inheritDoc
     */
    public function processItemData(string $rawData, ?string $path = null): array
    {
        $rawData = $this->processDataAsArray($rawData);
        $items = [];

        $keys = $rawData[self::KEYS];

        foreach ($rawData[self::DATA] as $data) {
            $itemData = array_combine($keys, $data);
            $item = $this->createItem();
            $item->addData($itemData);
            $items[$data[ItemInterface::SKU]] = $item;
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function processDataAsArray(string $rawData): array
    {
        return @json_decode($rawData, true) ?? [];
    }

    /**
     * @inheritDoc
     */
    protected function applyVariations(ItemInterface $item, array $rawData): void
    {
        // TODO: Implement applyVariations() method.
    }
}
