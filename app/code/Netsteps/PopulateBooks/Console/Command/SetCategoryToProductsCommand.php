<?php
namespace Netsteps\PopulateBooks\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
            ->setDescription('Loop through all products, get the subjectDDC number and put it to its correct category');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Load all products with their names only
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(['name', 'subjectDDC']);
        // 🔄 Load ALL categories with the custom attribute
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToSelect(['name', 'category_number_minimal', 'category_number_maximum']);

        $count = 0;
        foreach ($productCollection as $product) {
            if ($count < 1) {
                $name = $product->getName();
                $productSubjectDDC = floatval($product->getData('subjectDDC')); // custom attribute

                // 🔄 Loop all categories (menu)
//                $foundProductCategory = false;
                foreach ($categoryCollection as $category) {
                    $categoryNumberMinimal = $category->getData('category_number_minimal');
                    $categoryNumberMaximum = $category->getData('category_number_maximum');

                    $compareMessage = "";

                    if ($productSubjectDDC && $categoryNumberMinimal && $categoryNumberMaximum) {
                        if ($productSubjectDDC >= $categoryNumberMinimal && $productSubjectDDC < $categoryNumberMaximum) {
                            // ✅ Found a match → assign category
                            $categoryId = $category->getId();
                            $productId  = $product->getId();

                            try {
                                $this->appState->setAreaCode('adminhtml'); // or 'frontend'
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

    }
}
