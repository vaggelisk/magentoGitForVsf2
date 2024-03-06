<?php
/**
 * PaymentProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

use Netsteps\MarketplaceSales\Model\Order\Payment\MethodMapperInterface as PaymentMapper;
use Magento\Checkout\Api\PaymentInformationManagementInterface as PaymentInformationManagement;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface as GuestPaymentInformationManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\Quote\Payment;
use Magento\Quote\Model\Quote\PaymentFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteIdMask;

/**
 * Class PaymentProcessor
 * @package Netsteps\MarketplaceSales\Model\Order
 */
class PaymentProcessor extends AbstractAddressProcessor implements PaymentProcessorInterface
{
    /**
     * @var PaymentInformationManagement
     */
    private PaymentInformationManagement $_paymentInformationManagement;

    /**
     * @var GuestPaymentInformationManagement
     */
    private GuestPaymentInformationManagement $_guestPaymentInformationManagement;

    /**
     * @var PaymentFactory
     */
    private PaymentFactory $_paymentFactory;

    /**
     * @var QuoteIdMaskFactory
     */
    private QuoteIdMaskFactory $_quoteIdMaskedFactory;

    /**
     * @var PaymentMapper
     */
    private PaymentMapper $_paymentMapper;

    /**
     * @param AddressFactory $addressFactory
     * @param PaymentInformationManagement $paymentInformationManagement
     * @param GuestPaymentInformationManagement $guestPaymentInformationManagement
     * @param PaymentFactory $paymentFactory
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param PaymentMapper $paymentMapper
     */
    public function __construct(
        AddressFactory $addressFactory,
        PaymentInformationManagement $paymentInformationManagement,
        GuestPaymentInformationManagement $guestPaymentInformationManagement,
        PaymentFactory $paymentFactory,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        PaymentMapper $paymentMapper
    )
    {
        $this->_paymentInformationManagement = $paymentInformationManagement;
        $this->_guestPaymentInformationManagement = $guestPaymentInformationManagement;
        $this->_paymentFactory = $paymentFactory;
        $this->_quoteIdMaskedFactory = $quoteIdMaskFactory;
        $this->_paymentMapper = $paymentMapper;
        parent::__construct($addressFactory);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function preparePayment(
        \Magento\Quote\Model\Quote $cart,
        \Magento\Quote\Api\Data\AddressInterface $existingAddress,
        string $paymentMethodCode
    ): bool
    {
        $address = $this->createAddress($existingAddress);
        $customerId = (int)$cart->getCustomerId();
        $email = $cart->getCustomerEmail() ?? $existingAddress->getEmail();

        /** @var  $payment Payment */
        $payment = $this->_paymentFactory->create();
        $paymentMethodCode = $this->_paymentMapper->getMappedMethod($paymentMethodCode);
        $payment->setMethod($paymentMethodCode);

        if ($customerId === 0) {
            if (!$email) {
                throw new LocalizedException(
                   __('Missing email address for guest customer in quote %1.', $cart->getId())
                );
            }

            /** @var  $quoteMasked QuoteIdMask */
            $quoteMasked = $this->_quoteIdMaskedFactory->create();
            $quoteMasked->load($cart->getId(), 'quote_id');

            $this->_guestPaymentInformationManagement
                 ->savePaymentInformation($quoteMasked->getMaskedId(), $email, $payment, $address);
        } else {
            $this->_paymentInformationManagement
                ->savePaymentInformation($cart->getId(), $payment, $address);
        }

        return $this->_paymentMapper->needInvoice($paymentMethodCode);
    }
}
