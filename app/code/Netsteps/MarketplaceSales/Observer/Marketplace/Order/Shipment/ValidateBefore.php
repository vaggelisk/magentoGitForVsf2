<?php
/**
 * ValidateBefore
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\MarketplaceSales\Observer\Marketplace\Order\Shipment;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\MarketplaceApiAuth\Api\SellerAuthorizationInterface as SellerAuthorization;
use Netsteps\MarketplaceSales\Api\Data\OrderRelationInterface;
use Netsteps\MarketplaceSales\Exception\Order\ValidationException;

/**
 * Class ValidateBefore
 * @package Netsteps\MarketplaceSales\Observer\Marketplace\Order\Shipment
 */
class ValidateBefore implements ObserverInterface
{
    /**
     * @var SellerAuthorization
     */
    private SellerAuthorization $_authorization;

    /**
     * @param SellerAuthorization $authorization
     */
    public function __construct(SellerAuthorization $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Validate when try to ship an order via rest API.
     * Check if token used belong to a seller and also this order belongs to the same seller.
     * @inheritDoc
     * @throws ValidationException
     */
    public function execute(Observer $observer)
    {
        /** @var  $relation OrderRelationInterface */
        $relation = $observer->getRelation();
        $sellerId = $relation->getSellerId();
        $errorMessage = __('You are not authorized for this action.');

        if (is_null($sellerId)){
            throw new ValidationException($errorMessage);
        }

        $this->_authorization->isAllowed($sellerId, true, $errorMessage);
    }
}
