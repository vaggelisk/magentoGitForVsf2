<?php
/**
 * AbstractPlugin
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin\Marketplace\Webapi;

use Magento\Sales\Api\Data\OrderInterface;
use Netsteps\MarketplaceApiAuth\Api\SellerAuthorizationInterface;
use Netsteps\MarketplaceSales\Api\OrderRelationRepositoryInterface as OrderRelationRepository;
use Netsteps\MarketplaceSales\Exception\Order\ValidationException;

/**
 * Class AbstractPlugin
 * @package Netsteps\MarketplaceSales\Plugin\Marketplace\Webapi
 */
abstract class AbstractPlugin
{
    /**
     * @var SellerAuthorizationInterface
     */
    private SellerAuthorizationInterface $_auth;

    /**
     * @var OrderRelationRepository
     */
    private OrderRelationRepository $_relationRepository;

    /**
     * @param SellerAuthorizationInterface $authorization
     * @param OrderRelationRepository $orderRelationRepository
     */
    public function __construct(
        SellerAuthorizationInterface $authorization,
        OrderRelationRepository      $orderRelationRepository
    )
    {
        $this->_auth = $authorization;
        $this->_relationRepository = $orderRelationRepository;
    }

    /**
     * Check if order is allowed for any action
     * @param OrderInterface $order
     * @param string|null $message
     * @return void
     * @throws ValidationException
     */
    protected function _isAllowedOrder(OrderInterface $order, ?string $message = null): void
    {
        $relation = $this->_relationRepository->getRelationByOrderId($order->getEntityId());

        if (!$relation) {
            throw new ValidationException(
                __('There is no active relation for order %1.', $order->getIncrementId())
            );
        }

        $sellerId = $relation->getSellerId();

        if (!$sellerId) {
            throw new ValidationException(
                __('Order %1 is not related to a seller.', $order->getIncrementId())
            );
        }

        $message = $message ? __($message) : null;
        $this->_auth->isAllowed($sellerId, true, $message);
    }
}
