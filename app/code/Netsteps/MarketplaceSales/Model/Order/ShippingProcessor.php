<?php
/**
 * ShippingProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

use Magento\Checkout\Model\ShippingInformation;
use Magento\Checkout\Model\ShippingInformationFactory;
use Magento\Checkout\Api\ShippingInformationManagementInterface as ShippingInformationManagement;
use Magento\Quote\Model\Quote\AddressFactory;

/**
 * Class ShippingProcessor
 * @package Netsteps\MarketplaceSales\Model\Order
 */
class ShippingProcessor extends AbstractAddressProcessor implements ShippingProcessorInterface
{
    /**
     * @var ShippingInformationFactory
     */
    private ShippingInformationFactory $_shippingInformationFactory;

    /**
     * @var ShippingInformationManagement
     */
    private ShippingInformationManagement $_shippingInformationManagement;

    /**
     * @param AddressFactory $addressFactory
     * @param ShippingInformationFactory $shippingInformationFactory
     * @param ShippingInformationManagement $shippingInformationManagement
     */
    public function __construct(
        AddressFactory $addressFactory,
        ShippingInformationFactory $shippingInformationFactory,
        ShippingInformationManagement $shippingInformationManagement
    )
    {
        $this->_shippingInformationFactory = $shippingInformationFactory;
        $this->_shippingInformationManagement = $shippingInformationManagement;
        parent::__construct($addressFactory);
    }

    /**
     * @inheritDoc
     */
    public function prepare(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address $existingAddress,
        ?string $shippingMethodCode = null,
        ?string $shippingCarrierCode = null,
    ): void
    {
        /** @var  $shippingInformation  ShippingInformation */
        $shippingInformation = $this->_shippingInformationFactory->create();
        $address = $this->createAddress($existingAddress);

        $shippingInformation->setShippingAddress($address);

        if (!$shippingMethodCode || !$shippingCarrierCode) {
            $shippingMethodCode = 'freeshipping';
            $shippingCarrierCode = 'freeshipping';
        }

        $shippingInformation
            ->setShippingMethodCode($shippingMethodCode)
            ->setShippingCarrierCode($shippingCarrierCode);

        $this->_shippingInformationManagement->saveAddressInformation($quote->getId(), $shippingInformation);
    }
}
