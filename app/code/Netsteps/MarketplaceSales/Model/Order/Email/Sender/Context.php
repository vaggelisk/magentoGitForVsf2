<?php
/**
 * Context
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email\Sender;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Netsteps\Base\Helper\Email as EmailHelper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;

/**
 * Class Context
 * @package Netsteps\MarketplaceSales\Model\Order\Email\Sender
 */
class Context
{
    /**
     * @var EmailHelper
     */
    private EmailHelper $_emailSender;

    /**
     * @var EventManager
     */
    private EventManager $_eventManager;

    /**
     * @var AppEmulation
     */
    private AppEmulation $_appEmulation;

    /**
     * @var PaymentHelper
     */
    private PaymentHelper $_paymentHelper;

    /**
     * @var AddressRenderer
     */
    private AddressRenderer $_addressRenderer;

    /**
     * @var SellerRepository
     */
    private SellerRepository $_sellerRepository;

    /**
     * @param EmailHelper $emailHelper
     * @param EventManager $eventManager
     * @param AppEmulation $appEmulation
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param SellerRepository $sellerRepository
     */
    public function __construct(
        EmailHelper  $emailHelper,
        EventManager $eventManager,
        AppEmulation $appEmulation,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        SellerRepository $sellerRepository
    )
    {
        $this->_emailSender = $emailHelper;
        $this->_eventManager = $eventManager;
        $this->_appEmulation = $appEmulation;
        $this->_paymentHelper = $paymentHelper;
        $this->_addressRenderer = $addressRenderer;
        $this->_sellerRepository = $sellerRepository;
    }

    /**
     * @return EventManager
     */
    public function getEventManager(): EventManager
    {
        return $this->_eventManager;
    }

    /**
     * @return AppEmulation
     */
    public function getAppEmulation(): AppEmulation
    {
        return $this->_appEmulation;
    }

    /**
     * @return EmailHelper
     */
    public function getEmailSender(): EmailHelper
    {
        return $this->_emailSender;
    }

    /**
     * @return PaymentHelper
     */
    public function getPaymentHelper(): PaymentHelper
    {
        return $this->_paymentHelper;
    }

    /**
     * @return AddressRenderer
     */
    public function getAddressRenderer(): AddressRenderer
    {
        return $this->_addressRenderer;
    }

    /**
     * @return SellerRepository
     */
    public function getSellerRepository(): SellerRepository
    {
        return $this->_sellerRepository;
    }
}
