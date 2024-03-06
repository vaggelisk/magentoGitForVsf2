<?php
/**
 * AbstractAction
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\ManagerInterface;
use Netsteps\Logger\Model\Logger;
use Netsteps\Marketplace\Model\Adminhtml\Context;
use Netsteps\Marketplace\Model\Feed\ActionInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Netsteps\Marketplace\Model\Feed\Validation\ValidatorInterface;
use Netsteps\Marketplace\Model\Product\ItemProcessorInterface as ItemProcessor;
use Netsteps\Marketplace\Model\Product\ItemProcessorPoolInterface as ProcessorPool;
use Netsteps\Marketplace\Api\StockManagementInterface as StockManagement;
use Netsteps\Marketplace\Traits\Feed\ErrorHandleTrait;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface as SellerRepository;
use Netsteps\Marketplace\Model\Feed\Action\SubActionManagerInterface as SubActionManager;
use Netsteps\Marketplace\Model\Feed\Action\SubActionManagerInterfaceFactory as SubActionManagerFactory;

/**
 * Class AbstractAction
 * @package Netsteps\Marketplace\Model\Feed\Action
 */
abstract class AbstractAction implements ActionInterface
{
    use ErrorHandleTrait;

    const DEFAULT_PRODUCT_TYPE = 'simple';

    /**
     * @var Logger
     */
    protected Logger $_logger;

    /**
     * @var ValidatorInterface[]
     */
    private array $_validators;

    /**
     * Error pool
     * @var array
     */
    private array $_errors = [];

    /**
     * @var ItemProcessor
     */
    private ItemProcessor $_defaultProcessor;

    /**
     * @var ProcessorPool
     */
    private ProcessorPool $_itemProcessorPool;

    /**
     * @var StockManagement
     */
    protected StockManagement $_stockManagement;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $_eventManager;

    /**
     * @var SellerRepository
     */
    protected SellerRepository $_sellerRepository;

    /**
     * @var SubActionManagerFactory
     */
    private SubActionManagerFactory $_subActionManagerFactory;

    /**
     * @param Context $context
     * @param StockManagement $stockManagement
     * @param ItemProcessor $defaultProcessor
     * @param ProcessorPool $processorPool
     * @param SellerRepository $sellerRepository
     * @param SubActionManagerFactory $subActionManagerFactory
     * @param ValidatorInterface[] $validators
     */
    public function __construct(
        Context $context,
        StockManagement $stockManagement,
        ItemProcessor $defaultProcessor,
        ProcessorPool $processorPool,
        SellerRepository $sellerRepository,
        SubActionManagerFactory $subActionManagerFactory,
        array $validators = []
    )
    {
        $this->_logger = $context->getLogger();
        $this->_eventManager = $context->getEventManager();
        $this->_stockManagement = $stockManagement;
        $this->_defaultProcessor = $defaultProcessor;
        $this->_itemProcessorPool = $processorPool;
        $this->_sellerRepository = $sellerRepository;
        $this->_subActionManagerFactory = $subActionManagerFactory;
        $this->_validators = $validators;
        $this->_construct();
    }

    /**
     * Override this method instead of real __constructor
     * @return void
     */
    protected function _construct(): void {
        /**
         * Usage for inherited classes
         */
    }

    /**
     * @inheritDoc
     */
    public function validate(
        string $sellerGroup,
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
    ): array
    {
        $errors = [];

        if (array_key_exists($feed->getFileType(), $this->_validators)){
            $validator = $this->_validators[$feed->getFileType()];
            $errors = array_merge($errors, $validator->validateSchema($feed, $processor));
        }

        $this->setErrors($errors);

        return $this->getErrors();
    }

    /**
     * Get errors
     * @return array
     */
    protected function getErrors(): array {
        return $this->_errors;
    }

    /**
     * Set errors.
     * This is a hook to manage errors in inherited classes
     * @param array $errors
     * @return $this
     */
    protected function setErrors(array $errors): self {
        $this->_errors = $errors;
        return $this;
    }

    /**
     * Process single item
     * @param ItemInterface $item
     * @param SubActionManagerInterface $subActionManager
     * @return void
     */
    protected function processItem(ItemInterface $item, SubActionManagerInterface $subActionManager): void {
        $productType = $this->getProductType($item);
        $processor = $this->_itemProcessorPool->getProcessorByProductType($productType) ?? $this->_defaultProcessor;
        $processor->process($item, $subActionManager);
    }

    /**
     * Get product type
     * @param ItemInterface $item
     * @return string
     */
    protected function getProductType(ItemInterface $item): string {
        if(!empty($item->getVariations())){
            return Configurable::TYPE_CODE;
        }

        return self::DEFAULT_PRODUCT_TYPE;
    }

    /**
     * Create new sub action manager based on given seller
     * @param SellerInterface $seller
     * @return SubActionManagerInterface
     */
    protected function createSubActionManager(SellerInterface $seller): SubActionManager {
        return $this->_subActionManagerFactory->create(
            ['seller' => $seller]
        );
    }
}
