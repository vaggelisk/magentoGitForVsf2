<?php
/**
 * HistoryRepositoryInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Marketplace\Api\Data\ProductHistoryInterface;
use Netsteps\Marketplace\Api\ProductHistoryRepositoryInterface;
use Netsteps\Marketplace\Api\Data\ProductHistoryInterfaceFactory as HistoryFactory;
use Netsteps\Marketplace\Api\Data\ProductHistoryInterface as History;
use Netsteps\Marketplace\Model\ResourceModel\Product\History as HistoryResource;
use Netsteps\Marketplace\Model\ResourceModel\Product\History\CollectionFactory as HistoryCollectionFactory;
use Netsteps\Marketplace\Traits\EncryptionTrait;

/**
 * Interface HistoryRepositoryInterface
 * @package Netsteps\Marketplace\Model\Product
 */
class HistoryRepository implements ProductHistoryRepositoryInterface
{
    use EncryptionTrait;

    /**
     * Global static data map
     * array (
     *      sku => version_code
     *      ...
     * )
     * @var array|null
     */
    private static ?array $historyData = null;

    /**
     * Repository cached instances
     * @var array
     */
    private array $instances = [];

    /**
     * @var HistoryFactory
     */
    private HistoryFactory $_modelFactory;

    /**
     * @var HistoryCollectionFactory
     */
    private HistoryCollectionFactory $_modelCollectionFactory;

    /**
     * @var HistoryResource
     */
    private HistoryResource $_resource;

    /**
     * @param HistoryFactory $historyFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param HistoryResource $resource
     */
    public function __construct(
        HistoryFactory $historyFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        HistoryResource $resource
    )
    {
        $this->_modelFactory = $historyFactory;
        $this->_modelCollectionFactory = $historyCollectionFactory;
        $this->_resource = $resource;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function save(\Netsteps\Marketplace\Api\Data\ProductHistoryInterface $productHistory): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
    {
        $this->_resource->save($productHistory);
        return $this->get($productHistory->getProductSku(), true);
    }

    /**
     * @inheritDoc
     */
    public function get(string $sku, bool $force = false): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
    {
        if (array_key_exists($sku, $this->instances) && !$force){
            return $this->instances[$sku];
        }

        $productHistory = $this->createNewItem();
        $this->_resource->load($productHistory, $sku);

        if (!$productHistory->getId()) {
            throw new NoSuchEntityException(
                __('Product %1 does not have history.', $sku)
            );
        }

        $this->instances[$sku] = $productHistory;
        return $productHistory;
    }

    /**
     * @inheritDoc
     */
    public function deleteBySku(string $sku): bool
    {
        $productHistory = $this->get($sku);
        $this->_resource->delete($productHistory);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getAllHistoryData(): array
    {
        if(!is_array(self::$historyData)){
            $select = $this->_resource->getConnection()
                ->select()
                ->from(
                    ['main_table' => $this->_resource->getMainTable()],
                    [History::ID, History::VERSION_CODE]
                );

            self::$historyData = $this->_resource->getConnection()->fetchPairs($select);
        }

        return self::$historyData;
    }

    /**
     * @inheritDoc
     */
    public function createNewItem(): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
    {
        return $this->_modelFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function createHistoryItem(string $sku, array $data): \Netsteps\Marketplace\Api\Data\ProductHistoryInterface
    {
        $productHistory = $this->createNewItem();
        $productHistory->setProductSku($sku)->setVersionCode($this->createHash($data));
        return $this->save($productHistory);
    }

    /**
     * @inheritDoc
     */
    public function getHistory(array $skus = []): \Netsteps\Marketplace\Model\ResourceModel\Product\History\Collection
    {
        /** @var  $history \Netsteps\Marketplace\Model\ResourceModel\Product\History\Collection */
        $history = $this->_modelCollectionFactory->create();
        $history->addSkuFilter($skus);
        return $history;
    }

    /**
     * @inheritDoc
     */
    public function isNeededUpdate(string $sku, array $data): bool
    {
        if (!$this->isProductExists($sku)){
            return false;
        }

        $currentVersion = $this->getAllHistoryData()[$sku];
        return $currentVersion !== $this->createHash($data);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateVersion(string $sku, array $data): bool
    {
        return (bool)$this->_resource->getConnection()
            ->update(
                $this->_resource->getMainTable(),
                [ProductHistoryInterface::VERSION_CODE => $this->createHash($data)],
                [ProductHistoryInterface::ID . ' = ?' => $sku]
            );
    }

    /**
     * @inheritDoc
     */
    public function isProductExists(string $sku): bool
    {
        return array_key_exists($sku, $this->getAllHistoryData());
    }
}
