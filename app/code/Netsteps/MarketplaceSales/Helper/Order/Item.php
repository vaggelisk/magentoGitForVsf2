<?php
/**
 * Item
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Helper\Order;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Marketplace\Model\Product\Attribute\Source\EstimatedDelivery as EstimatedDeliverySource;
use Netsteps\MarketplaceSales\Traits\OrderItemDataManagementTrait;
use Netsteps\MarketplaceSales\Model\Order\Item\Email\Data as ItemEmailData;
use Magento\Framework\App\Helper\Context;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;

/**
 * Class Item
 * @package Netsteps\MarketplaceSales\Helper\Order
 */
class Item extends AbstractHelper
{
    use OrderItemDataManagementTrait;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @var EstimatedDeliverySource
     */
    private EstimatedDeliverySource $_estimatedDeliverySource;

    /**
     * @param Context $context
     * @param SellerRepository $sellerRepository
     * @param EstimatedDeliverySource $estimatedDelivery
     */
    public function __construct(
        Context $context,
        SellerRepository $sellerRepository,
        EstimatedDeliverySource $estimatedDelivery
    )
    {
        $this->_sellerRepository = $sellerRepository;
        $this->_estimatedDeliverySource = $estimatedDelivery;
        parent::__construct($context);
    }

    /**
     * Get order item email data
     * @param \Magento\Sales\Model\Order\Item $item
     * @return ItemEmailData
     */
    public function getEmailData(\Magento\Sales\Model\Order\Item $item): ItemEmailData {
        $emailData = new ItemEmailData();

        $sellerInfo = $this->exportSellerInfoData($item)['seller_info'] ?? [];

        if (empty($sellerInfo)){
            return $emailData;
        }

        $emailData->setSellerName($this->getSellerName($sellerInfo))
            ->setEstimatedDelivery($this->getEstimatedDeliveryLabel($sellerInfo))
            ->setSpecialPrice($this->getSpecialPrice($sellerInfo))
            ->setPrice($this->getSpecialPrice($sellerInfo));

        return $emailData;
    }

    /**
     * Get seller name
     * @param array $sellerInfo
     * @return string|null
     */
    private function getSellerName(array $sellerInfo): ?string {
        try {
            $seller = $this->_sellerRepository->getById((int)$sellerInfo['seller_id']);
            return $seller->getName();
        } catch (NoSuchEntityException $e) {
            return '-';
        }
    }

    /**
     * Get estimation delivery label
     * @param array $sellerInfo
     * @return string|null
     */
    private function getEstimatedDeliveryLabel(array $sellerInfo): ?string {
       return $this->_estimatedDeliverySource->getOptionText(
            $sellerInfo['delivery_id'] ?? EstimatedDeliverySource::DELIVERY_AVAILABLE
        );
    }

    /**
     * Get special price if exists
     * @param array $sellerInfo
     * @return float|null
     */
    private function getSpecialPrice(array $sellerInfo): ?float {
        $specialPrice = $sellerInfo['special_price'] ?? null;
        return $specialPrice ? (float)$specialPrice : null;
    }

    /**
     * Get special price if exists
     * @param array $sellerInfo
     * @return float|null
     */
    private function getPrice(array $sellerInfo): ?float {
        $price = $sellerInfo['price'] ?? null;
        return $price ? (float)$price : null;
    }
}
