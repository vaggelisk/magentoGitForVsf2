<?php
/**
 * AttributeManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Netsteps\Marketplace\Model\Product\Item\Processor\Context;
use Magento\Eav\Api\AttributeOptionManagementInterface as AttributeOptionManagement;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory as AttributeOptionFactory;

/**
 * Class AttributeManagement
 * @package Netsteps\Marketplace\Model\Product
 */
class AttributeManagement implements AttributeManagementInterface
{
    /**
     * Option map for attributes
     * @var array
     */
    private static array $attributeOptionMap = [];

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private ProductAttributeRepositoryInterface $_attributeRepository;

    /**
     * @var AttributeOptionManagement
     */
    private AttributeOptionManagement $_attributeOptionManagement;

    /**
     * @var AttributeOptionFactory
     */
    private AttributeOptionFactory $_optionFactory;

    /**
     * @param Context $context
     * @param AttributeOptionManagement $attributeOptionManagement
     * @param AttributeOptionFactory $attributeOptionFactory
     */
    public function __construct(
        Context                   $context,
        AttributeOptionManagement $attributeOptionManagement,
        AttributeOptionFactory    $attributeOptionFactory
    )
    {
        $this->_attributeRepository = $context->getAttributeRepository();
        $this->_attributeOptionManagement = $attributeOptionManagement;
        $this->_optionFactory = $attributeOptionFactory;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionId(string $attributeCode, string $label): ?int
    {
        if (isset(self::$attributeOptionMap[$attributeCode])) {
            $optionKey = array_search($label, self::$attributeOptionMap[$attributeCode]);

            if ($optionKey !== false) {
                return (int)$optionKey;
            }
        }

        $attribute = $this->_attributeRepository->get($attributeCode);

        if (!$attribute->getSourceModel()) {
            return null;
        }

        /** @var  $source \Magento\Eav\Model\Entity\Attribute\Source\Table */
        $source = $attribute->getSource();

        $optionId = null;

        $normalizedLabel = mb_strtolower($label);

        foreach ($source->getAllOptions(false) as $option) {
            if ($normalizedLabel === html_entity_decode(mb_strtolower($option['label']))) {
                $optionId = (int)$option['value'];
                break;
            }
        }

        if (is_null($optionId)) {
            return null;
        }

        self::$attributeOptionMap[$attributeCode][$optionId] = $label;

        return $optionId;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createOption(string $attributeCode, string $label, ?int $sortOrder = null): ?int
    {
        /** @var  $option \Magento\Eav\Model\Entity\Attribute\Option */
        $option = $this->_optionFactory->create();
        $option->setLabel($label);

        $optionId = (int)$this->_attributeOptionManagement->add(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            $option
        );

        self::$attributeOptionMap[$attributeCode][$optionId] = $label;

        return $optionId;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductAttribute(string $attributeCode): \Magento\Catalog\Api\Data\ProductAttributeInterface
    {
        return $this->_attributeRepository->get($attributeCode);
    }
}
