<?php
/**
 * SaveDistributorDataToIndex
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Plugin\Product;

use Netsteps\Marketplace\Model\Processor\Product\MerchantProcessorInterface as MerchantProcessor;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Psr\Log\LoggerInterface;

/**
 * Class SaveDistributorDataToIndex
 * @package Netsteps\Marketplace\Plugin\Product
 */
class SaveDistributorDataToIndex
{
    /**
     * @var MerchantProcessor
     */
    private MerchantProcessor $_merchantProcessor;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @param MerchantProcessor $merchantProcessor
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        MerchantProcessor $merchantProcessor,
        LoggerPool $loggerPool
    )
    {
        $this->_merchantProcessor = $merchantProcessor;
        $this->_logger = $loggerPool->getLogger('debug');
    }

    /**
     * Process product after save to update the seller_product_index table
     * with distributor data
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Catalog\Model\ResourceModel\Product $result
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Model\ResourceModel\Product
     */
    public function afterSave(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\ResourceModel\Product $result,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ): \Magento\Catalog\Model\ResourceModel\Product
    {
        try {
            $this->_merchantProcessor->processProduct($product);
        } catch (\Exception $e) {
            $this->_logger->error(
                __('Error on product save distributor index. Reason: %1', $e->getMessage())
            );
        }

        return $result;
    }
}
