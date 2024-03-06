<?php
/**
 * SellerManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Product;

use Netsteps\Marketplace\Api\Data\MerchantDataInterface;
use Netsteps\Marketplace\Api\ProductIndexRepositoryInterface as ProductIndexRepository;
use Netsteps\Marketplace\Model\Data\MerchantItemData;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\MarketplaceSales\Exception\Quote\CanNotAddToCartException;
use Netsteps\MarketplaceSales\Model\Product\StockItemRepositoryInterface as ProductStockRepository;
use Netsteps\MarketplaceSales\Traits\ProductDataManagementTrait;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Psr\Log\LoggerInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;

/**
 * Class SellerManagement
 * @package Netsteps\MarketplaceSales\Model\Product
 */
class SellerManagement implements SellerManagementInterface
{
    use ProductDataManagementTrait;

    /**
     * @var ProductIndexRepository
     */
    private ProductIndexRepository $_productIndexRepository;

    /**
     * @var StockItemRepositoryInterface
     */
    private StockItemRepositoryInterface $_productStockRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @param ProductIndexRepository $productIndexRepository
     * @param StockItemRepositoryInterface $productStockRepository
     * @param SellerRepository $sellerRepository
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        ProductIndexRepository $productIndexRepository,
        ProductStockRepository $productStockRepository,
        SellerRepository       $sellerRepository,
        LoggerPool             $loggerPool
    )
    {
        $this->_productIndexRepository = $productIndexRepository;
        $this->_productStockRepository = $productStockRepository;
        $this->_sellerRepository = $sellerRepository;
        $this->_logger = $loggerPool->getLogger('order');
    }

    /**
     * @inheritDoc
     * @throws CanNotAddToCartException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function initProductForSeller(
        \Magento\Catalog\Model\Product $product,
        int                            $sellerId,
        \Magento\Framework\DataObject  $request
    ): ?\Netsteps\Marketplace\Api\Data\MerchantDataInterface
    {
        if ($this->canProceed($product)) {
            $qty = $request->hasQty() ? (float)($request->getQty()) : null;
            $productSellerData = $this->_productIndexRepository->getBestSellerDataByProductId($product->getId(), $qty);

            if (!is_null($productSellerData)) {
                if ($productSellerData->getSellerId() !== $sellerId) {
                    $this->_logger->error(
                        __('Can not add the product %1 to cart. Mismatch product info.', $product->getName())
                    );

                    throw new CanNotAddToCartException(
                        __('Product data %1 has changed! Visit your cart to see the prices and the available seller!', $product->getName())
                    );
                }

                $this->_productStockRepository->get(
                    $product->getSku(),
                    $productSellerData->getSourceCode(),
                    $qty,
                    true
                );

                $this->addSellerInfoBuyRequest(
                    $product,
                    $productSellerData->toArray([
                        MerchantDataInterface::SELLER_ID,
                        MerchantDataInterface::PRICE,
                        MerchantDataInterface::SPECIAL_PRICE,
                        MerchantDataInterface::DELIVERY_ID,
                        MerchantDataInterface::SOURCE_CODE,
                        MerchantDataInterface::EAN,
                    ])
                );

                if ($product->getFinalPrice() >= $productSellerData->getFinalPrice()) {

                    /** This row set the custom price to product */
                    $request->setCustomPrice($productSellerData->getFinalPrice());

                    $product->setPrice($productSellerData->getPrice())
                        ->setSpecialPrice($productSellerData->getSpecialPrice());

                }
                return $productSellerData;
            } else {

                throw new CanNotAddToCartException(
                    __('Product that you are trying to add is not available.')
                );
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function isValid(\Magento\Catalog\Model\Product $product, int $sellerId, float $qty): bool
    {
        //TODO implementation
        return true;
    }

    /**
     * Check if can proceed product
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function canProceed(\Magento\Catalog\Model\Product $product): bool
    {
        return in_array($product->getTypeId(), MerchantItemData::getAvailableProductTypesToIndex());
    }

    /**
     * Add seller info buy request to product options
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     * @return void
     * @throws CanNotAddToCartException
     */
    private function addSellerInfoBuyRequest(\Magento\Catalog\Model\Product $product, array $data)
    {
        $customOptions = $product->getCustomOptions();
        $infoBuyRequest = $customOptions['info_buyRequest'] ?? null;

        if (!$infoBuyRequest) {
            throw new CanNotAddToCartException(
                __('Missing buy request data')
            );
        }

        $infoBuyRequestData = json_decode($infoBuyRequest->getValue(), true);
        $infoBuyRequestData = array_merge($infoBuyRequestData, ['seller_info' => $data]);
        $infoBuyRequest->setValue(json_encode($infoBuyRequestData));
    }
}
