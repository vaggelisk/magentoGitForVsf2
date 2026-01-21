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

class ListProductsWithoutImageCommand extends Command
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
        $this->setName('netsteps:populatebooks:list-products-without-image')
            ->setDescription('List products that do not have a catalog image assigned yet')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Maximum number of products to display', self::MAX_DISPLAYED_PRODUCTS);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (LocalizedException $e) {
            $this->logger->warning('Area code already set: ' . $e->getMessage());
        }

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(['name', 'sku', 'image']);
        $productCollection->addMediaGalleryData();

        $productsWithoutImage = [];
        foreach ($productCollection as $product) {
            $image = $product->getImage();
            $hasImage = $image && $image !== 'no_selection';

            if (!$hasImage) {
                $gallery = $product->getMediaGalleryEntries();
                if (!is_array($gallery) || count($gallery) === 0) {
                    $productsWithoutImage[] = $product;
                }
            }
        }

        $count = count($productsWithoutImage);
        $this->logger->info("Found {$count} product(s) without an image");

        if ($count === 0) {
            $output->writeln('<info>Every product already has at least one catalog image.</info>');
            return Command::SUCCESS;
        }

        $limitOption = (int)$input->getOption('limit');
        if ($limitOption <= 0) {
            $limitOption = self::MAX_DISPLAYED_PRODUCTS;
        }

        $displayCount = min($limitOption, $count);
        $output->writeln("<comment>Products without image assigned: {$count}</comment>");
        if ($displayCount < $count) {
            $output->writeln("<comment>Showing first {$displayCount} entries (use --limit or -l to adjust).</comment>");
        }

        foreach (array_slice($productsWithoutImage, 0, $displayCount) as $product) {
            $output->writeln(sprintf(
                'ID %s | SKU %s | Name %s',
                $product->getId(),
                $product->getSku(),
                $product->getName()
            ));
        }

        return Command::SUCCESS;
    }
}