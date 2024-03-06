<?php
/**
 * ProductSellerData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Console;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Netsteps\Base\Console\AbstractCommand;
use Netsteps\MarketplaceSales\Traits\DataCastTrait;
use Symfony\Component\Console\Input\InputOption;
use Netsteps\MarketplaceSales\Api\ProductManagementInterface as ProductManagement;

/**
 * Class ProductSellerData
 * @package Netsteps\MarketplaceSales\Console
 */
class ProductSellerData extends AbstractCommand
{
    use DataCastTrait;

    const PRODUCT_IDS = 'product_ids';

    const ALL_PRODUCTS = 'do_all';

    protected $_commandName = 'marketplace:product-data:update';

    protected $_commandDescription = 'Update product lowest seller offer data';

    protected $_options = [
        self::PRODUCT_IDS => [
            'label' => 'Product Ids',
            'required' => InputOption::VALUE_OPTIONAL
        ],
        self::ALL_PRODUCTS => [
            'label' => 'Do All Products',
            'required' => InputOption::VALUE_OPTIONAL
        ]
    ];

    /**
     * @var ProductManagement
     */
    private ProductManagement $_productManagement;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private ProductRepositoryInterface $_productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param ProductManagement $productManagement
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param string|null $name
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductManagement $productManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        string $name = null
    )
    {
        $this->_productManagement = $productManagement;
        $this->_productRepository = $productRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function _execute()
    {
        $this->_output->writeln('<info>Start update seller data process...</info>');
        $productIds = $this->_getProductIds();

        try {
            $this->_productManagement->updateProductsData($productIds);
            $this->_output->writeln('<info>Process finished!</info>');
        } catch (\Exception $e) {
            $this->_output->writeln("<error>{$e->getMessage()}</error>");
        }
    }

    /**
     * @return array
     */
    private function _getAllProducts(): array
    {
        $products = $this->_productRepository->getList($this->_searchCriteriaBuilder->create())->getItems();

        return array_map(function (ProductInterface $product){
            return $product->getId();
        }, $products);
    }

    /**
     * Get product ids
     * @return int[]
     */
    private function _getProductIds(): array {
        $productIds = $this->_input->getOption(self::PRODUCT_IDS);

        $doAll = $this->_input->getOption(self::ALL_PRODUCTS);

        if (!$productIds && !$doAll){
            return [];
        }

        if($productIds){
            $productIds = explode(',', $productIds);

            return array_map(
                [$this, 'castStringToInt'],
                array_filter($productIds, [$this, 'isPositive'])
            );
        }
        else{
            return $this->_getAllProducts();
        }
    }

    /**
     * Check if id is positive number
     * @param string $id
     * @return bool
     */
    private function isPositive(string $id): bool {
        return is_numeric($id) && (int)trim($id) > 0;
    }
}
