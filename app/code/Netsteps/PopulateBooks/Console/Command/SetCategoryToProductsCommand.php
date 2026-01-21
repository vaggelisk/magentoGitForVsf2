<?php
namespace Netsteps\PopulateBooks\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\State;

class SetCategoryToProductsCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;


    /**
     * @var State
     */
    private $appState;

    public function __construct(
        LoggerInterface $logger,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        ProductRepositoryInterface $productRepository,
        State $appState
    ) {
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productRepository = $productRepository;
        $this->appState = $appState;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('netsteps:populatebooks:setcategorytoproducts')
            ->setDescription('Loop through all products, get the subjectDDC number and put it to its correct category')
            ->addOption('start-id', null, InputOption::VALUE_OPTIONAL, 'Start iterating from this product ID (inclusive)', 0)
            ->addOption('end-id', null, InputOption::VALUE_OPTIONAL, 'Stop iterating at this product ID (inclusive)', 0);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Load all products with their names only
        $productCollection = $this->productCollectionFactory->create();
        $startId = (int)$input->getOption('start-id');
        $endId = (int)$input->getOption('end-id');

        if ($startId > 0) {
            $productCollection->addFieldToFilter('entity_id', ['gteq' => $startId]);
        }

        if ($endId > 0) {
            $productCollection->addFieldToFilter('entity_id', ['lteq' => $endId]);
        }
        $productCollection->addAttributeToSelect(['name', 'subjectDDC']);
        // 🔄 Load ALL categories with the custom attribute
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToSelect(['name', 'category_number_minimal', 'category_number_maximum']);

        $this->appState->setAreaCode('adminhtml'); // or 'frontend'

        
        $count = 0;
        foreach ($productCollection as $product) {
            if ($count < 700) { // Limit to first 700 products for testing
                $name = $product->getName();
                $productSubjectDDC = floatval($product->getData('subjectDDC')); // custom attribute

                // 🔄 Loop all categories (menu)
//                $foundProductCategory = false;
                foreach ($categoryCollection as $category) {
                    $categoryNumberMinimal = $category->getData('category_number_minimal');
                    $categoryNumberMaximum = $category->getData('category_number_maximum');
                    $price = $product->getPrice(); // Base price (regular price)
                    $finalPrice = $product->getFinalPrice(); // After special price, catalog rules, etc.
                    
                    $compareMessage = "";

                    if ($productSubjectDDC && $categoryNumberMinimal && $categoryNumberMaximum) {
                        if ($productSubjectDDC >= $categoryNumberMinimal && $productSubjectDDC < $categoryNumberMaximum) {
                            // ✅ Found a match → assign category
                            $categoryId = $category->getId();
                            $productId  = $product->getId();

                            try {
                                $existingCategoryIds = $product->getCategoryIds();

                                // ✅ Get this category + all its parents
                                $categoryPathIds = $category->getPathIds(); // e.g. [1,2,6] → root, default, subcategory
                                // Remove root category (ID 1) if you don’t want to assign it
                                $categoryPathIds = array_filter($categoryPathIds, function ($id) {
                                    return $id > 2;
                                });

                                // Merge with existing categories
                                $newCategoryIds = array_unique(array_merge($existingCategoryIds, $categoryPathIds));
                                $product->setCategoryIds($newCategoryIds);

                                if (!$product->getPrice() || $product->getPrice() <= 0) {
                                    $product->setPrice(100); // Set price to 100
                                }
                                                                
                                $this->productRepository->save($product);


                                $assignMsg = "   -> MATCH ✅ Assigned category '{$category->getName()}' (ID {$categoryId}) and its parents ["
                                    . implode(',', $categoryPathIds) . "] to product '{$name}' (ID {$productId})";


                                $output->writeln($assignMsg);
                                $this->logger->info($assignMsg);

                            } catch (\Exception $e) {
                                $errorMsg = "   -> Error assigning category ID {$categoryId} to product {$name}: " . $e->getMessage();
                                $output->writeln("<error>$errorMsg</error>");
                                $this->logger->error($errorMsg);
                            }



//                            $foundProductCategory = true;
                        }
                    }
                    $output->writeln($compareMessage);
                }

                $message = "Product: {$name} | subjectDDC: " . ($productSubjectDDC ?: 'N/A') ." | einai to subjectDDC < 1000: " . ( $productSubjectDDC ? ($productSubjectDDC<1000) : 'N/A') ;
                // Print to console
                $output->writeln($message);

                // Log into system.log
                $this->logger->info("Product: " . $message);
            }
            $count++;
        }

        $output->writeln("<comment>Total products processed: $count</comment>");
        $this->logger->info("Total products processed: " . $count);

        return Command::SUCCESS;
    }
}
