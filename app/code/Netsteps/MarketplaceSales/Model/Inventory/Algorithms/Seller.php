<?php
/**
 * Seller
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Inventory\Algorithms;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionItemInterface as SourceSelectionItem;
use Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionItemInterfaceFactory as SourceSelectionItemFactory;
use Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface;
use Magento\InventorySourceSelectionApi\Model\SourceSelectionInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface as GetAssignedSources;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Exception\Quote\ValidationException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
/**
 * Class Seller
 * @package Netsteps\MarketplaceSales\Model\Inventory\Algorithms
 */
class Seller implements SourceSelectionInterface
{
    /**
     * @var GetAssignedSources
     */
    private GetAssignedSources $_getAssignedSources;

    /**
     * @var SourceSelectionResultInterfaceFactory
     */
    private SourceSelectionResultInterfaceFactory $_selectionResultFactory;

    /**
     * @var SourceSelectionItemFactory
     */
    private SourceSelectionItemFactory $_sourceSelectionItemFactory;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $_connection;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param GetAssignedSources $getAssignedSources
     * @param SourceSelectionResultInterfaceFactory $selectionResultFactory
     * @param SourceSelectionItemFactory $sourceSelectionItemFactory
     * @param ResourceConnection $resourceConnection
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        GetAssignedSources $getAssignedSources,
        SourceSelectionResultInterfaceFactory $selectionResultFactory,
        SourceSelectionItemFactory $sourceSelectionItemFactory,
        ResourceConnection $resourceConnection,
        LoggerPool $loggerPool
    )
    {
        $this->_selectionResultFactory = $selectionResultFactory;
        $this->_getAssignedSources = $getAssignedSources;
        $this->_sourceSelectionItemFactory = $sourceSelectionItemFactory;
        $this->_connection = $resourceConnection->getConnection();
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @inheritDoc
     */
    public function execute(InventoryRequestInterface $inventoryRequest): SourceSelectionResultInterface
    {
        $itemsToDeliver = [];

        foreach ($inventoryRequest->getItems() as $item) {
            $itemsToDeliver[$item->getSku()] = $item->getQty();
        }

        $sourceItemSelections = $this->getSourceItems($inventoryRequest, $itemsToDeliver);

        $isShippable = true;
        foreach ($itemsToDeliver as $itemToDeliver) {
            if (!$this->isZero($itemToDeliver)) {
                $isShippable = false;
                break;
            }
        }

        return $this->_selectionResultFactory->create(
            [
                'sourceItemSelections' => $sourceItemSelections,
                'isShippable' => $isShippable
            ]
        );
    }

    /**
     * Get result stock reduction items
     * @param InventoryRequestInterface $inventoryRequest
     * @param array $itemsToDeliver
     * @return array
     * @throws LocalizedException
     * @throws ValidationException
     * @throws \Magento\Framework\Exception\InputException
     */
    private function getSourceItems(InventoryRequestInterface $inventoryRequest, array &$itemsToDeliver): array {
        $sourceItems = [];

        $allAvailableSources = $this->_getAssignedSources->execute($inventoryRequest->getStockId());
        $availableCodes = $this->extractSellerSourceCodes($inventoryRequest);

        $availableSources = array_filter($allAvailableSources, function ($source) use ($availableCodes){
            return $source->isEnabled() && in_array($source->getSourceCode(), $availableCodes);
        });

        /** @var \Magento\InventorySourceSelection\Model\Request\ItemRequest $item */
        foreach ($inventoryRequest->getItems() as $item) {
            $sellerSourceCode = $item->getData('seller_source');
            $sku = $item->getSku();

            if (!$sellerSourceCode) {
                throw new LocalizedException(
                    __('Missing seller source code for item %1', $item->getSku())
                );
            }

            $key = "{$sku}-{$sellerSourceCode}";

            if (array_key_exists($key, $sourceItems)){
                continue;
            }

            $availableQty = $this->getAvailableQuantity($item->getSku(), $sellerSourceCode, $availableSources);
            $qtyToDeduct = min($availableQty, $itemsToDeliver[$sku] ?? 0.0);

            $sourceItem = $this->_sourceSelectionItemFactory->create(
                [
                    'sourceCode' => $sellerSourceCode,
                    'sku' => $sku,
                    'qtyToDeduct' => $qtyToDeduct,
                    'qtyAvailable' => $availableQty
                ]
            );

            //Uncomment this line to debug each item data before stock deduction
            //It writes in var/log/netsteps/marketplace/order.log
            //$this->debugItem($sourceItem);

            $sourceItems[$key] = $sourceItem;
            $itemsToDeliver[$sku] -= $qtyToDeduct;
        }

        return array_values($sourceItems);
    }

    /**
     * Get distinct source codes that are used in
     * @param InventoryRequestInterface $inventoryRequest
     * @return string[]
     */
    private function extractSellerSourceCodes(InventoryRequestInterface $inventoryRequest): array {
        $codes = [];

        /** @var \Magento\InventorySourceSelection\Model\Request\ItemRequest $item */
        foreach ($inventoryRequest->getItems() as $item){
            if ($sourceCode = $item->getData('seller_source')) {
                $codes[] = $sourceCode;
            }
        }

        return array_unique($codes);
    }

    /**
     * Get the available quantity for one sku and a specific source code
     * @param string $sku
     * @param string $sellerSourceCode
     * @param \Magento\InventoryApi\Api\Data\SourceInterface[] $availableSources
     * @return float
     * @throws ValidationException
     */
    private function getAvailableQuantity(string $sku, string $sellerSourceCode, array $availableSources): float {
        $isValid = false;

        foreach ($availableSources as $source) {
            if ($sellerSourceCode === $source->getSourceCode()) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            throw new ValidationException(
                __('Seller source code %1 is not enabled or not be assigned in the current website.', $sellerSourceCode)
            );
        }

        $select = $this->_connection->select()
            ->from(
                ['source_item' => $this->_connection->getTableName('inventory_source_item')],
                ['quantity']
            )
            ->where('source_item.source_code = ?', $sellerSourceCode)
            ->where('source_item.sku = ?', $sku)
            ->where('source_item.status = ?', 1);

        return (float)$this->_connection->fetchOne($select);
    }

    /**
     * Compare float number with some epsilon
     *
     * @param float $floatNumber
     *
     * @return bool
     */
    private function isZero(float $floatNumber): bool
    {
        return $floatNumber < 0.0000001;
    }

    /**
     * Debug an item
     * @param SourceSelectionItem $item
     * @return void
     */
    private function debugItem(SourceSelectionItem $item): void {
        $this->_logger->info('Sku: ' . $item->getSku());
        $this->_logger->info('Source Code: ' . $item->getSourceCode());
        $this->_logger->info('Available Qty: ' . $item->getQtyAvailable());
        $this->_logger->info('Deduct Qty: ' . $item->getQtyToDeduct());
        $this->_logger->info('----------------------------------------');
    }
}
