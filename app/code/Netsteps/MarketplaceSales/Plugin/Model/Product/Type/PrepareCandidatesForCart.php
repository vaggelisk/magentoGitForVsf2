<?php
/**
 * ValidateProductBeforeAddToCart
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Model\Product\Type;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Netsteps\Marketplace\Model\Logger\LoggerPoolInterface as LoggerPool;
use Netsteps\MarketplaceSales\Exception\Quote\CanNotAddToCartException;
use Netsteps\MarketplaceSales\Model\Product\SellerManagementInterface;
use Netsteps\MarketplaceSales\Model\Product\SellerManagementInterface as ProductSellerManager;
use Netsteps\MarketplaceSales\Traits\ProductDataManagementTrait;
use Psr\Log\LoggerInterface;

/**
 * Class ValidateProductBeforeAddToCart
 * @package Netsteps\MarketplaceSales\Plugin\Quote
 */
class PrepareCandidatesForCart
{
    use ProductDataManagementTrait;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * @var ProductSellerManager
     */
    private ProductSellerManager $_productSellerManager;

    /**
     * @param ProductSellerManager $productSellerManager
     * @param LoggerPool $loggerPool
     */
    public function __construct(
        ProductSellerManager $productSellerManager,
        LoggerPool           $loggerPool
    )
    {
        $this->_productSellerManager = $productSellerManager;
        $this->_logger = $loggerPool->getLogger('quote');
    }

    /**
     * After prepare product for cart
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $productType
     * @param array|string $result
     * @param DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string|null $processMode
     * @return \Magento\Catalog\Model\Product[]|string
     * @throws CanNotAddToCartException
     * @throws LocalizedException
     */
    public function afterPrepareForCartAdvanced(
        \Magento\Catalog\Model\Product\Type\AbstractType $productType,
        array|string                                     $result,
        \Magento\Framework\DataObject                    $buyRequest,
        \Magento\Catalog\Model\Product                   $product,
        ?string                                          $processMode = null
    ): array|string
    {
        if (!is_array($result) || $product->getData('ignore_seller_preparation')) {
            return $result;
        }

        $sellerId = $buyRequest->getData(SellerManagementInterface::INFO_BUY_REQUEST_SELLER_ID);

        $this->_logger->critical(__('sellerId: ', $sellerId));

        $sellerId = 1;

        if (!$sellerId) {
            $this->_logger->critical('Geia sou Maria');
            $this->_logger->critical(__('No seller id provided for product: %1', $product->getSku()));

            throw new LocalizedException(
                __('Product that you are trying to add is not available.')
            );
        }

        $sellerId = (int)$sellerId;
        $isValid = false;

        /** @var  $candidate \Magento\Catalog\Model\Product */
        foreach ($result as $candidate) {
            $isValid = $this->_productSellerManager->initProductForSeller($candidate, $sellerId, $buyRequest) || $isValid;
        }

        if (!$isValid) {
            throw new CanNotAddToCartException(
                __(
                    'Can not add product %1 to cart right now. Please try again in a while.',
                    $product->getName()
                )
            );
        }

        return $result;
    }
}
