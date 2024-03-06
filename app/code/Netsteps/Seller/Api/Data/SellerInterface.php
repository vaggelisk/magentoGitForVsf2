<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Api\Data;

interface SellerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS = 'status';
    const NAME = 'name';
    const EMAIL = 'email';
    const GROUP = 'group';
    const SOURCE_CODE = 'source_code';
    const OPTIONS = 'options';
    const FEEDS = 'feeds';
    const DISTRIBUTOR_CODE = 'ns_distributor';

    /**
     * @return mixed
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getCreatedAt():string;

    /**
     * @param string $data
     * @return $this
     */
    public function setCreatedAt(string $data): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return string
     */
    public function getUpdatedAt():string;

    /**
     * @param string $data
     * @return $this
     */
    public function setUpdatedAt(string $data): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return string
     */
    public function getGroup():string;

    /**
     * @param string $group
     * @return $this
     */
    public function setGroup(string $group): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return string
     */
    public function getSourceCode():string;

    /**
     * @param string $sourceCode
     * @return $this
     */
    public function setSourceCode(string $sourceCode): \Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return \Netsteps\Seller\Api\Data\SellerOptionInterface[]
     */
    public function getOptions():array;

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options):\Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return \Netsteps\Seller\Api\Data\SellerFeedInterface[]
     */
    public function getFeeds():array;

    /**
     * @param array $feeds
     * @return $this
     */
    public function setFeeds(array $feeds):\Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * @return \Netsteps\Seller\Api\Data\SellerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Netsteps\Seller\Api\Data\SellerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Netsteps\Seller\Api\Data\SellerExtensionInterface $extensionAttributes);

}

