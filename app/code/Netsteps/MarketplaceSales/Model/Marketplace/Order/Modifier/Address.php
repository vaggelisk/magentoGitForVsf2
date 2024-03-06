<?php
/**
 * Address
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier;

use Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface as OrderAddress;
use Netsteps\MarketplaceSales\Api\Data\OrderAddressInterfaceFactory as OrderAddressFactory;

/**
 * Class Address
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier
 */
class Address extends AbstractModifier
{
    /**
     * @var OrderAddressFactory
     */
    private OrderAddressFactory $_addressFactory;

    /**
     * @param Context $context
     * @param OrderAddressFactory $addressFactory
     */
    public function __construct(Context $context, OrderAddressFactory $addressFactory)
    {
        parent::__construct($context);
        $this->_addressFactory = $addressFactory;
    }

    /**
     * @inheritDoc
     * @var \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Netsteps\MarketplaceSales\Api\Data\OrderInterface $orderData,
        ?\Magento\Sales\Api\Data\OrderInterface $parentOrder = null
    ): void
    {
        $billingAddress = $this->createAddressDataFromOrderAddress($order->getBillingAddress());

        /** Dispatch event before set billing address */
        $this->_eventManager->dispatch(
            'marketplace_order_before_set_billing_address',
            ['order' => $order, 'billing_address' => $billingAddress]
        );

        $orderData->setBillingAddress($billingAddress);

        if (!$order->getIsVirtual()) {
            $shippingAddress = $this->createAddressDataFromOrderAddress($order->getShippingAddress());

            /** Dispatch event before set shipping address */
            $this->_eventManager->dispatch(
                'marketplace_order_before_set_shipping_address',
                ['order' => $order, 'billing_address' => $billingAddress]
            );

            $orderData->setShippingAddress($shippingAddress);
        }
    }

    /**
     * Create an order data object based on order's address (billing or shipping)
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $address
     * @return OrderAddress
     */
    private function createAddressDataFromOrderAddress(\Magento\Sales\Api\Data\OrderAddressInterface $address): OrderAddress {
        /** @var  $addressData OrderAddress */
        $addressData = $this->_addressFactory->create();

        $addressData->setEmail($address->getEmail())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setStreet(implode(' ', $address->getStreet()))
            ->setPostcode($address->getPostcode())
            ->setCity($address->getCity())
            ->setRegion($address->getRegion())
            ->setCountryId($address->getCountryId())
            ->setTelephone($address->getTelephone());

        return $addressData;
    }
}
