<?php
/**
 * Address
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order;

use Magento\Framework\DataObject;
use Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

/**
 * Class Address
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order
 */
class Address extends DataObject implements OrderAddressInterface
{

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return $this->_getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function getFirstname(): string
    {
        return $this->_getData(self::FIRSTNAME);
    }

    /**
     * @inheritDoc
     */
    public function getLastname(): string
    {
        return $this->_getData(self::LASTNAME);
    }

    /**
     * @inheritDoc
     */
    public function getStreet(): string
    {
        return $this->_getData(self::STREET);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode(): string
    {
        return $this->_getData(self::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function getCity(): string
    {
        return $this->_getData(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function getRegion(): string
    {
        return $this->_getData(self::REGION);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId(): string
    {
        return $this->_getData(self::COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone(): string
    {
        return $this->_getData(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(): array
    {
        return $this->_getData(self::METADATA) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function setFirstname(string $firstname): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * @inheritDoc
     */
    public function setLastname(string $lastname): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * @inheritDoc
     */
    public function setStreet(string $street): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode(string $postcode): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function setCity(string $city): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function setRegion(string $region): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId(string $countryId): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone(string $telephone): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
    {
        return $this->setData(self::METADATA, $metadata);
    }
}
