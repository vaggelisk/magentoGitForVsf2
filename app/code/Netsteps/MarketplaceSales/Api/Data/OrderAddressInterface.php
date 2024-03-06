<?php
/**
 * OrderAddressInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Api\Data;

/**
 * Interface OrderAddressInterface
 * @package Netsteps\MarketplaceSales\Api\Data
 */
interface OrderAddressInterface
{
    const EMAIL = 'email';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const STREET = 'street';
    const POSTCODE = 'postcode';
    const CITY = 'city';
    const REGION = 'region';
    const COUNTRY_ID = 'country_id';
    const TELEPHONE = 'telephone';
    const METADATA = 'metadata';

    /**
     * Get address email
     * @return string
     */
    public function getEmail(): string;

    /**
     * Get address firstname
     * @return string
     */
    public function getFirstname(): string;

    /**
     * Get address lastname
     * @return string
     */
    public function getLastname(): string;

    /**
     * Get address street
     * @return string
     */
    public function getStreet(): string;

    /**
     * Get address postcode
     * @return string
     */
    public function getPostcode(): string;

    /**
     * Get address city
     * @return string
     */
    public function getCity(): string;

    /**
     * Get address region
     * @return string
     */
    public function getRegion(): string;

    /**
     * Get address country_id
     * @return string
     */
    public function getCountryId(): string;

    /**
     * Get address telephone
     * @return string
     */
    public function getTelephone(): string;

    /**
     * Get address metadata
     * @return \Netsteps\MarketplaceSales\Api\Data\MetadataInterface[]
     */
    public function getMetadata(): array;

    /**
     * Set address email
     * @param string $email
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setEmail(string $email): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address firstname
     * @param string $firstname
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setFirstname(string $firstname): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address lastname
     * @param string $lastname
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setLastname(string $lastname): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address street
     * @param string $street
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setStreet(string $street): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address postcode
     * @param string $postcode
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setPostcode(string $postcode): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address city
     * @param string $city
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setCity(string $city): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address region
     * @param string $region
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setRegion(string $region): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address country_id
     * @param string $countryId
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setCountryId(string $countryId): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address telephone
     * @param string $telephone
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setTelephone(string $telephone): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;

    /**
     * Set address metadata
     * @param array $metadata
     * @return \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface
     */
    public function setMetadata(array $metadata): \Netsteps\MarketplaceSales\Api\Data\OrderAddressInterface;
}


