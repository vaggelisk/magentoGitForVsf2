<?php
/**
 * SellerAuthorization
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceApiAuth
 */

namespace Netsteps\MarketplaceApiAuth\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Netsteps\MarketplaceApiAuth\Api\SellerAuthorizationInterface;
use Netsteps\MarketplaceApiAuth\Exception\MarketplaceAuthorizationException;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;

use Magento\Authorization\Model\CompositeUserContext as UserContext;
use Magento\Authorization\Model\UserContextInterface;
use Netsteps\Seller\Api\SellerAdminRepositoryInterface as SellerAdminRepository;
use Netsteps\Seller\Api\SellerIntegrationRepositoryInterface as SellerIntegrationRepository;

/**
 * Class SellerAuthorization
 * @package Netsteps\MarketplaceApiAuth\Model
 */
class SellerAuthorization implements SellerAuthorizationInterface
{
    /**
     * @var SellerIntegrationRepository
     */
    private SellerIntegrationRepository $_sellerIntegrationRepository;

    /**
     * @var SellerAdminRepository
     */
    private SellerAdminRepository $_sellerAdminRepository;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @var UserContext
     */
    private UserContext $_userContext;

    /**
     * @param SellerIntegrationRepository $sellerIntegrationRepository
     * @param SellerAdminRepository $sellerAdminRepository
     * @param SellerRepository $sellerRepository
     * @param UserContext $userContext
     */
    public function __construct(
        SellerIntegrationRepository $sellerIntegrationRepository,
        SellerAdminRepository $sellerAdminRepository,
        SellerRepository $sellerRepository,
        UserContext $userContext
    )
    {
        $this->_sellerIntegrationRepository = $sellerIntegrationRepository;
        $this->_sellerAdminRepository = $sellerAdminRepository;
        $this->_sellerRepository = $sellerRepository;
        $this->_userContext = $userContext;
    }

    /**
     * @inheritDoc
     * @throws MarketplaceAuthorizationException
     */
    public function isAllowed(int $sellerId, bool $raiseException = false, ?Phrase $message = null): bool
    {
        $userId = $this->_userContext->getUserId();
        $userType = $this->_userContext->getUserType();

        if (!$userId){
            return false;
        }

        try {
            $userSellerId = $this->getSellerIdByUser($userId, $userType);
            $isAllowed = $userSellerId === $sellerId;
        } catch (\Exception $e) {
            return false;
        }

        if (!$isAllowed && $raiseException){
            $errorMessage = $message ?? __(self::DEFAULT_MESSAGE, $this->getSellerName($userSellerId));
            throw new MarketplaceAuthorizationException($errorMessage);
        }

        return $isAllowed;
    }

    /**
     * Get seller id by user
     * @param int $userId
     * @param int $userType
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getSellerIdByUser(int $userId, int $userType): ?int {
        return match ($userType) {
            UserContextInterface::USER_TYPE_INTEGRATION => $this->_sellerIntegrationRepository->getByIntegrationId($userId)->getSellerId(),
            UserContextInterface::USER_TYPE_ADMIN => $this->_sellerAdminRepository->getByUserId($userId)->getSellerId(),
            default => null,
        };
    }

    /**
     * Get seller name
     * @param int $id
     * @return string
     */
    private function getSellerName(int $id): string {
        try {
            return $this->_sellerRepository->getById($id)->getName();
        } catch (NoSuchEntityException $e) {
            return 'Unknown';
        }
    }
}
