<?php
namespace Netsteps\PopulateBooks\Console\Command;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListProductsWithoutCategoryCommand extends Command
{
    private const MAX_DISPLAYED_PRODUCTS = 30;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        LoggerInterface $logger,
        ProductCollectionFactory $productCollectionFactory,
        State $appState
    ) {
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->appState = $appState;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('netsteps:populatebooks:list-unassigned-products')
            ->setDescription('Show products that do not belong to any category yet')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Maximum number of products to display', self::MAX_DISPLAYED_PRODUCTS);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (LocalizedException $e) {
            // Area code is already set in CLI context; ignore the exception.
            $this->logger->warning('Area code already set: ' . $e->getMessage());
        }

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(['name', 'sku', 'category_ids']);

        $unassignedProducts = [];
        foreach ($productCollection as $product) {
            $categoryIds = $product->getCategoryIds();
            if (empty($categoryIds)) {
                $unassignedProducts[] = $product;
            }
        }

        $count = count($unassignedProducts);
        $this->logger->info("Found {$count} product(s) without categories");

        if ($count === 0) {
            $output->writeln('<info>All products already belong to at least one category.</info>');
            return Command::SUCCESS;
        }

        $output->writeln("<comment>Products without category assignments: {$count}</comment>");

        $limitOption = (int)$input->getOption('limit');
        if ($limitOption <= 0) {
            $limitOption = self::MAX_DISPLAYED_PRODUCTS;
        }

        $displayCount = min($limitOption, $count);
        if ($displayCount < $count) {
            $output->writeln("<comment>Showing first {$displayCount} products (use --limit or -l to change the number).</comment>");
        }

        foreach (array_slice($unassignedProducts, 0, $displayCount) as $product) {
            $output->writeln(sprintf(
                "ID %s | SKU %s | Name %s",
                $product->getId(),
                $product->getSku(),
                $product->getName()
            ));
        }

        return Command::SUCCESS;
    }
}