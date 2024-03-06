<?php
/**
 * Product
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier;

use Netsteps\MarketplaceSales\Api\Data\MetadataInterface;
use Netsteps\MarketplaceSales\Api\Data\OrderProductInterface as OrderProduct;
use Netsteps\MarketplaceSales\Api\Data\OrderProductInterfaceFactory as OrderProductFactory;
use Netsteps\MarketplaceSales\Model\Product\SellerManagementInterface;
use Netsteps\MarketplaceSales\Traits\OrderItemDataManagementTrait;
use Magento\Eav\Api\AttributeRepositoryInterface as AttributeRepository;

/**
 * Class Product
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier
 */
class Product extends AbstractModifier
{
    use OrderItemDataManagementTrait;

    /**
     * @var OrderProductFactory
     */
    private OrderProductFactory $_productFactory;

    /**
     * @var AttributeRepository
     */
    private AttributeRepository $_attributeRepository;

    /**
     * @param Context $context
     * @param OrderProductFactory $orderProductFactory
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(
        Context $context,
        OrderProductFactory $orderProductFactory,
        AttributeRepository $attributeRepository
    )
    {
        parent::__construct($context);
        $this->_productFactory = $orderProductFactory;
        $this->_attributeRepository = $attributeRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @var \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Netsteps\MarketplaceSales\Api\Data\OrderInterface $orderData,
        ?\Magento\Sales\Api\Data\OrderInterface $parentOrder = null
    ): void
    {
        $products = [];

        foreach ($order->getItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            /** @var  $productData OrderProduct */
            $productData = $this->_productFactory->create();
            $productData->setItemId($item->getItemId())
                ->setSku($item->getProduct()->getSku())
                ->setPrice($this->getItemOriginalPrice($item))
                ->setPriceAfterDiscount($item->getPriceInclTax())
                ->setQty($item->getQtyOrdered())
                ->setQtyRefunded($item->getQtyRefunded())
                ->setQtyShipped($item->getQtyShipped())
                ->setRowTotal($item->getRowTotalInclTax())
                ->setName($item->getName())
                ->setVatPercent($item->getTaxPercent())
                ->setVatValue($item->getTaxAmount())
                ->setEan($this->getItemOriginalEan($item))
                ->setOptions($this->getProductOptions($item));

            $products[] = $productData;
        }

        $orderData->setProducts($products);
    }

    /**
     * Get product options
     * @param \Magento\Sales\Model\Order\Item $item
     * @return MetadataInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductOptions(\Magento\Sales\Model\Order\Item $item): array {
        $options = $item->getProductOptions();

        if (!is_array($options) || !isset($options['attributes_info'])) {
            return [];
        }

        $productOptions = [];

        foreach ($options['attributes_info'] as $option){
            $attribute = $this->_attributeRepository->get(
                \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
                $option['option_id']
            );

            $metadataOption = $this->createMetadataOption();
            $metadataOption->setValue($option['value'])->setCode($attribute->getAttributeCode());
            $productOptions[] = $metadataOption;
        }

        return $productOptions;
    }
}
