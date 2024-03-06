<?php
/**
 * AbstractAddressProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\AddressFactory;

/**
 * Class AbstractAddressProcessor
 * @package Netsteps\MarketplaceSales\Model\Order
 */
abstract class AbstractAddressProcessor
{
    /**
     * @var AddressFactory
     */
    private AddressFactory $_addressFactory;

    /**
     * @param AddressFactory $addressFactory
     */
    public function __construct(AddressFactory $addressFactory)
    {
        $this->_addressFactory = $addressFactory;
    }

    /**
     * Create a new address only with necessary data for customer
     * @param Address $address
     * @return Address
     */
    protected function createAddress(\Magento\Quote\Model\Quote\Address $address): \Magento\Quote\Model\Quote\Address {
        /** @var  $newAddress \Magento\Quote\Model\Quote\Address */
        $newAddress = $this->_addressFactory->create();

        $rawData = $address->toArray([
            AddressInterface::KEY_FIRSTNAME,
            AddressInterface::KEY_LASTNAME,
            AddressInterface::KEY_MIDDLENAME,
            AddressInterface::KEY_EMAIL,
            AddressInterface::KEY_COUNTRY_ID,
            AddressInterface::KEY_TELEPHONE,
            AddressInterface::KEY_POSTCODE,
            AddressInterface::KEY_CITY,
            AddressInterface::KEY_REGION_ID,
            AddressInterface::KEY_REGION_CODE,
            AddressInterface::KEY_REGION,
            AddressInterface::KEY_STREET,
            AddressInterface::KEY_COMPANY,
            AddressInterface::KEY_FAX,
            AddressInterface::CUSTOMER_ADDRESS_ID,
            AddressInterface::KEY_CUSTOMER_ID
        ]);

        unset($rawData['rates'], $rawData['items'], $rawData['totals']);

        $newAddress->setData($rawData);

        return $newAddress;
    }
}
