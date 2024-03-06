<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Netsteps\Seller\Api\Data\SellerGroupInterface;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Model\Seller\SellerProcessorInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;

class Seller extends AbstractExtensibleModel implements SellerInterface
{
    protected $_eventPrefix = 'ns_seller';

    protected $_eventObject = 'seller';

    protected $_cacheTag = 'NS_SL';

    private array $loadProcessors;

    private array $saveProcessors;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $loadProcessors
     * @param array $saveProcessors
     * @param array $data
     */
    public function __construct(
        Context                     $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory  $extensionFactory,
        AttributeValueFactory       $customAttributeFactory,
        AbstractResource            $resource = null,
        AbstractDb                  $resourceCollection = null,
        array                       $loadProcessors = [],
        array                       $saveProcessors = [],
        array                       $data = []
    )
    {
        $this->loadProcessors = $loadProcessors;
        $this->saveProcessors = $saveProcessors;
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
    }


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Netsteps\Seller\Model\ResourceModel\Seller::class);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->getData(SellerInterface::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $data): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::CREATED_AT, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(SellerInterface::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $data): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::UPDATED_AT, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): bool
    {
        return (bool)$this->getData(SellerInterface::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(bool $status): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::STATUS, (int)$status);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->getData(SellerInterface::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::NAME, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return (string)$this->getData(SellerInterface::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::EMAIL, $email);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return (string)$this->getData(SellerInterface::GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setGroup(string $group): \Netsteps\Seller\Api\Data\SellerInterface
    {
        if (!in_array($group, SellerGroupInterface::AVAILABLE_GROUPS)) {
            $group = SellerGroupInterface::GROUP_DEFAULT;
        }
        $this->setData(SellerInterface::GROUP, $group);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSourceCode(): string
    {
        return $this->getData(SellerInterface::SOURCE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setSourceCode(string $sourceCode): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::SOURCE_CODE, $sourceCode);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->getData(SellerInterface::OPTIONS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::OPTIONS, $options);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFeeds(): array
    {
        return $this->getData(SellerInterface::FEEDS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setFeeds(array $feeds): \Netsteps\Seller\Api\Data\SellerInterface
    {
        $this->setData(SellerInterface::FEEDS, $feeds);
        return $this;
    }

    /**
     * @return Seller
     */
    public function afterLoad()
    {
        /** @var SellerProcessorInterface $processor */
        foreach ($this->loadProcessors as $processor) {
            $processor->execute($this);
        }
        return parent::afterLoad();
    }

    /**
     * @return Seller
     */
    public function afterSave(): Seller
    {
        /** @var SellerProcessorInterface $processor */
        foreach ($this->saveProcessors as $processor) {
            $processor->execute($this);
        }
        return parent::afterSave();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Netsteps\Seller\Model\Seller\Validator\Email::class);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(\Netsteps\Seller\Api\Data\SellerExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
