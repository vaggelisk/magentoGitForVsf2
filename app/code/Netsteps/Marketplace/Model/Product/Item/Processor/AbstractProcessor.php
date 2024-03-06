<?php
/**
 * AbstractProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product\Item\Processor;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Api\Data\OptionInterfaceFactory;
use Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory;
use Magento\ConfigurableProduct\Api\OptionRepositoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Netsteps\Marketplace\Api\ProductHistoryRepositoryInterface;
use Netsteps\Marketplace\Api\StockManagementInterface;
use Netsteps\Marketplace\Model\Data\ExporterInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Product\ImageProcessor;
use Netsteps\Marketplace\Model\Product\ItemProcessorInterface;
use Netsteps\Marketplace\Model\Product\Item\Processor\Context as ProcessorContext;
use Netsteps\Marketplace\Model\Product\AttributeManagementInterface as AttributeManagement;
use Netsteps\Marketplace\Model\Product\Item\ManagementInterface as ProductManagement;
use Netsteps\Marketplace\Traits\AttributeProcessorTrait;
use Netsteps\Marketplace\Traits\StringModifierTrait;

/**
 * Class AbstractProcessor
 * @package Netsteps\Marketplace\Model\Product\Item\Processor
 */
abstract class AbstractProcessor implements ItemProcessorInterface
{
    use AttributeProcessorTrait;
    use StringModifierTrait;

    /**
     * @var string[]
     */
    protected array $optionAttributes;

    /**
     * @var ProductFactory
     */
    private ProductFactory $_productFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $_productCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $_productRepository;

    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $_connection;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var ProductHistoryRepositoryInterface
     */
    protected ProductHistoryRepositoryInterface $_productHistoryRepository;

    /**
     * @var ExporterInterface
     */
    protected ExporterInterface $_dataExporter;

    /**
     * @var AttributeManagement
     */
    protected AttributeManagement $_attributeManagement;

    /**
     * @var ImageProcessor
     */
    protected ImageProcessor $_productImageProcessor;

    /**
     * @var OptionRepositoryInterface
     */
    protected OptionRepositoryInterface $_configurableOptionRepository;

    /**
     * @var OptionInterfaceFactory
     */
    protected OptionInterfaceFactory $_configurableOptionFactory;

    /**
     * @var OptionValueInterfaceFactory
     */
    protected OptionValueInterfaceFactory $_configurableOptionValueFactory;

    /**
     * @var StockManagementInterface
     */
    protected StockManagementInterface $_stockManagement;

    /**
     * @var ProductManagement
     */
    protected ProductManagement $_productManagement;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $_eventManager;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface
     */
    protected ProductAttributeMediaGalleryManagementInterface $_mediaGalleryManagement;

    /**
     * @param Context $processorContext
     * @param AttributeManagement $attributeManagement
     * @param ProductManagement $productManagement
     * @param ExporterInterface $dataExporter
     * @param \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface $mediaGalleryManagement
     * @param string[] $optionAttributes
     */
    public function __construct(
        ProcessorContext $processorContext,
        AttributeManagement $attributeManagement,
        ProductManagement $productManagement,
        ExporterInterface $dataExporter,
        ProductAttributeMediaGalleryManagementInterface $mediaGalleryManagement,
        array $optionAttributes = []
    ) {
        $this->_productFactory = $processorContext->getProductFactory();
        $this->_productCollectionFactory = $processorContext->getProductCollectionFactory();
        $this->_productRepository = $processorContext->getProductRepository();
        $this->_connection = $processorContext->getConnection();
        $this->_storeManager = $processorContext->getStoreManager();
        $this->_productHistoryRepository = $processorContext->getProductHistoryRepository();
        $this->_productImageProcessor = $processorContext->getProductImageProcessor();
        $this->_configurableOptionRepository = $processorContext->getConfigurableOptionRepository();
        $this->_configurableOptionFactory = $processorContext->getConfigurableOptionFactory();
        $this->_configurableOptionValueFactory = $processorContext->getConfigurableOptionValueFactory();
        $this->_stockManagement = $processorContext->getStockManagement();
        $this->_eventManager = $processorContext->getEventManager();
        $this->_attributeManagement = $attributeManagement;
        $this->_productManagement = $productManagement;
        $this->_dataExporter = $dataExporter;
        $this->_mediaGalleryManagement = $mediaGalleryManagement;
        $this->optionAttributes = $optionAttributes;
    }

    /**
     * Create a new product object
     * @return \Magento\Catalog\Model\Product
     */
    protected function createProduct(): \Magento\Catalog\Model\Product {
        return $this->_productFactory->create();
    }

    /**
     * Create a new product collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function createProductCollection(): \Magento\Catalog\Model\ResourceModel\Product\Collection {
        return $this->_productCollectionFactory->create();
    }

    /**
     * Create sku for
     * @param ItemInterface $item
     * @param array $parts
     * @return string
     */
    protected function createSkuForItem(ItemInterface $item, array $parts = []): string {
        array_unshift($parts, $item->getSku());
        return implode('-', $parts);
    }



    /**
     * Process product images
     * @param \Magento\Catalog\Model\Product $product
     * @param ItemInterface $item
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processImages(
        \Magento\Catalog\Model\Product $product,
        \Netsteps\Marketplace\Model\Feed\ItemInterface $item
    ): bool {
        if ($image = $item->getImage()) {
            $product = $this->processProductImage($product, $image, ['image', 'small_image', 'thumbnail']);
        }

        $additionalImages = $item->getAdditionalImages();

        if (!empty($additionalImages)){
            $images = $additionalImages['image'];

            if (!is_array($images)){
                $images = [$images];
            }

            foreach ($images as $additionalImage) {
                $product = $this->processProductImage($product, $additionalImage);
            }
        }

        $this->_productRepository->save($product);

        return (bool)$product->getData('needs_reindex');
    }

    /**
     * Create new configurable option
     * @return \Magento\ConfigurableProduct\Api\Data\OptionInterface
     */
    protected function createConfigurableOption(): \Magento\ConfigurableProduct\Api\Data\OptionInterface {
        return $this->_configurableOptionFactory->create();
    }

    /**
     * Create a new configurable product option value
     * @return \Magento\ConfigurableProduct\Api\Data\OptionValueInterface
     */
    protected function createConfigurableOptionValue(): \Magento\ConfigurableProduct\Api\Data\OptionValueInterface {
        return $this->_configurableOptionValueFactory->create();
    }

    /**
     * @inheritDoc
     */
    protected function getAttributeManagement(): \Netsteps\Marketplace\Model\Product\AttributeManagementInterface
    {
        return $this->_attributeManagement;
    }

    /**
     * Create url key
     * @param string $name
     * @param string $sku
     * @return string
     */
    protected function createUrlKey(string $name, string $sku): string {
        $urlKey = "{$name}-{$sku}";
        return $this->toGreeklish($urlKey);
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $imageUrl
     * @param array $types
     * @param bool $isVisible
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processProductImage(ProductInterface $product, string $imageUrl, array $types = [], bool $isVisible = true): ProductInterface
    {
        //TODO: Add logic for updating images
        $this->_productImageProcessor->execute($product, $imageUrl, $isVisible, $types);
        $product->setData('needs_reindex', true);

        return $product;
    }

    /**
     * @param string|null $filePath
     * @return string|null
     */
    protected function getImageName(?string $filePath): ?string
    {
        if(empty($filePath)) return null;
        $pathParts = explode('/', $filePath);

        $name = end($pathParts);
        $name = strtoupper(str_replace(' ', '_', $name));
        $name = explode('.', $name);
        return reset($name);
    }
}
