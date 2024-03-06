<?php
/**
 * ApplySellerCalculationAlgorithm
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Inventory;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Psr\Log\LoggerInterface;

/**
 * Class ApplySellerCalculationAlgorithm
 * @package Netsteps\MarketplaceSales\Plugin\Inventory
 */
class ApplySellerCalculationAlgorithm
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param LoggerPool $loggerPool
     */
    public function __construct(LoggerPool $loggerPool) {
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * Change algorithm to 'seller' by force if marketplace sales module is enabled
     * We want it by default because we should reduce qty from seller's source_code.
     *
     * @param \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface $sourceSelectionService
     * @param callable $proceed
     * @param \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
     * @param string $algorithmCode
     * @return \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
     */
    public function aroundExecute(
        \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface $sourceSelectionService,
        callable $proceed,
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest,
        string $algorithmCode
    ): \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
    {
        $algorithmCode = 'seller';
        return $proceed($inventoryRequest, $algorithmCode);
    }
}
