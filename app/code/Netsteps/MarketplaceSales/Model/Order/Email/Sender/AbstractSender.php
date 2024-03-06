<?php
/**
 * AbstractSender
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email\Sender;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Netsteps\MarketplaceSales\Model\Order\Email\SenderInterface;
use Netsteps\Base\Helper\Email as EmailHelper;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Netsteps\MarketplaceSales\Model\Order\Email\IdentityInterface as Identity;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Netsteps\MarketplaceSales\Traits\OrderDataManagementTrait;
use Netsteps\MarketplaceSales\Traits\OrderItemDataManagementTrait;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;


/**
 * Class AbstractSender
 * @package Netsteps\MarketplaceSales\Model\Order\Email\Sender
 */
abstract class AbstractSender implements SenderInterface
{
    use OrderDataManagementTrait;
    use OrderItemDataManagementTrait;

    /**
     * @var EmailHelper
     */
    protected EmailHelper $_emailSender;

    /**
     * @var Identity
     */
    protected Identity $_identity;

    /**
     * @var EventManager
     */
    protected EventManager $_eventManager;

    /**
     * @var AppEmulation
     */
    protected AppEmulation $_appEmulation;

    /**
     * @var PaymentHelper
     */
    private PaymentHelper $_paymentHelper;

    /**
     * @var AddressRenderer
     */
    private AddressRenderer $_addressRenderer;

    /**
     * @var string
     */
    protected string $_eventPrefix = 'marketplace_sales_email';

    /**
     * @var SellerRepository
     */
    protected SellerRepository $_sellerRepository;

    /**
     * @param Context $context
     * @param Identity $identity
     */
    public function __construct(
        \Netsteps\MarketplaceSales\Model\Order\Email\Sender\Context $context,
        Identity                                                    $identity
    )
    {
        $this->_emailSender = $context->getEmailSender();
        $this->_eventManager = $context->getEventManager();
        $this->_appEmulation = $context->getAppEmulation();
        $this->_paymentHelper = $context->getPaymentHelper();
        $this->_addressRenderer = $context->getAddressRenderer();
        $this->_sellerRepository = $context->getSellerRepository();
        $this->_identity = $identity;
    }

    /**
     * @inheritDoc
     */
    public function send(\Magento\Sales\Api\Data\OrderInterface $order): void
    {
        $storeId = (int)$order->getStoreId();

        if ($this->_identity->isEnabled($storeId)) {
            $this->_appEmulation->startEnvironmentEmulation($storeId);

            $template = $this->_identity->getTemplate($storeId);
            $sender = $this->_identity->getSender($storeId);
            $recipient = $this->getRecipient($order);
            $variables = $this->getTemplateVariables($order);

            $this->_emailSender->sendEmail(
                $template,
                $variables,
                $sender,
                $recipient
            );

            $this->_appEmulation->stopEnvironmentEmulation();
        }
    }

    /**
     * Get recipient
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    protected function getRecipient(\Magento\Sales\Api\Data\OrderInterface $order): array {
        $fullName = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        return ['name' => $fullName, 'email' => $order->getCustomerEmail()];
    }

    /**
     * Return payment info block as html
     *
     * @param Order $order
     * @return string
     * @throws \Exception
     */
    protected function getPaymentHtml(Order $order): string
    {
        return $this->_paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStoreId()
        );
    }

    /**
     * Render shipping address into html.
     *
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress(Order $order): ?string
    {
        return $order->getIsVirtual()
            ? null
            : $this->_addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Render billing address into html.
     *
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress(Order $order): ?string
    {
        return $this->_addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Get seller data
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    protected function getSellerData(\Magento\Sales\Api\Data\OrderInterface $order): array
    {
        $relation = $this->getOrderRelation($order);

        if (!$relation || !$relation->getSellerId()){
            return [];
        }

        try {
            $seller = $this->_sellerRepository->getById($relation->getSellerId());
            return [
                'name' => $seller->getName(),
                'email' => $seller->getEmail()
            ];
        } catch (\Exception $e){
            return [];
        }
    }

    /**
     * Get template variables
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    abstract protected function getTemplateVariables(\Magento\Sales\Api\Data\OrderInterface $order): array;
}
