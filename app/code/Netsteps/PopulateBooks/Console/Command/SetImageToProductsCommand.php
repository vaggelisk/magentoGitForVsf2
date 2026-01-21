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
use Magento\Catalog\Model\Product\Gallery\Processor;
use Magento\Framework\Filesystem\Io\File;

class SetImageToProductsCommand extends Command
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


    private Processor $galleryProcessor;

    private File $file;


    /**
     * @var State
     */
    private $appState;

    public function __construct(
        LoggerInterface $logger,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Processor $galleryProcessor,
        File $file,
        State $appState
    ) {
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productRepository = $productRepository;
        $this->galleryProcessor = $galleryProcessor;
        $this->file = $file;
        $this->appState = $appState;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('netsteps:populatebooks:setimagetoproducts')
            ->setDescription('Loop through all products, get the image uri and put it to its correspond product')
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
        $productCollection->addAttributeToSelect(['name', 'coverimage']);
        $productCollection->addMediaGalleryData();

        $this->appState->setAreaCode('adminhtml'); // or 'frontend'


        $output->writeln('<info>Starting image assignment...</info>');

        try {
            $count = 0;
            $biblionet = "https://biblionet.gr";
            foreach ($productCollection as $product) {

                $name = $product->getName();
                $id = $product->getId();
                $coverImage = $product->getData('coverimage'); // custom attribute
                $brokenImage = $coverImage ? substr_count($coverImage, '.jpg') >= 2 : false;
                $placeholderImage = '/assets/images/placeholders/blank_page_cover.jpg';
                $isPlaceholder = $coverImage && trim($coverImage) === $placeholderImage;

                if ($coverImage && !$brokenImage && !$isPlaceholder && $count < 10) {

                    $imageUrl = $biblionet.$coverImage;

                    // ✅ Step 1: Define safe Magento import dir
                    $mediaImportDir = BP . '/pub/media/import';
                    if (!is_dir($mediaImportDir)) {
                        mkdir($mediaImportDir, 0777, true);
                    }

                    // ✅ 2. Build file path
                    $fileName = basename(parse_url($imageUrl, PHP_URL_PATH)); // e.g. 214782.jpg
                    $fileNameNoExt = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '', $fileName);  // e.g. 214782

                    $localFilePath = $mediaImportDir . '/' . $fileName;

               
                    // ✅ 3. Check if already exists locally
                    if (!file_exists($localFilePath)) {
                        $output->writeln("<comment>Downloading image...</comment>");
                        $fileContent = @file_get_contents($imageUrl);
                        if ($fileContent === false) {
                            throw new \Exception("Cannot download image from {$imageUrl}");
                        }
                        file_put_contents($localFilePath, $fileContent);
                    } else {
                        $output->writeln("<info>Image already exists locally: {$fileName}</info>");
                    }   
                    
                    // // 3.5 Remove all the previous leftover
                    // $product->setMediaGalleryEntries([]); // remove all images
                    // $this->productRepository->save($product);


                    // ✅ 4. Check if product already has that image
                    $existingImages = $product->getMediaGalleryEntries();
                    $output->writeln('Existing images count: ' . count((array)$existingImages));

                    $alreadyHasImage = false;

                    if (is_array($existingImages)) {
                        foreach ($existingImages as $entry) {

                            $output->writeln("<info> {$entry->getFile()} </info>");
                            $output->writeln("<info> {$fileName} </info>");

                            if (strpos($entry->getFile(), $fileNameNoExt) !== false) {
                                $alreadyHasImage = true;
                                break;
                            }
                        }
                    }

                    if ($alreadyHasImage) {
                        $output->writeln("<info>Product already has this image, skipping...</info>");
                    } else {    // Add image to product
                        $this->galleryProcessor->addImage(
                            $product,
                            $localFilePath,
                            ['image', 'small_image', 'thumbnail'],
                            false,
                            false
                        );


                        // ✅ 6. Get the relative path that Magento just stored
                        $mediaGallery = $product->getMediaGalleryEntries();
                        $newImageFile = null;

                        if (is_array($mediaGallery) && count($mediaGallery) > 0) {
                            $lastImage = end($mediaGallery);
                            $newImageFile = $lastImage->getFile(); // e.g. /2/1/214782_1.jpg
                        }

                        if ($newImageFile) {
                            // ✅ 7. Force Magento to use the *base* file path
                            $product->setData('small_image', $newImageFile);
                            $product->setData('thumbnail', $newImageFile);
                            $product->setData('image', $newImageFile);
                        }

                        $this->productRepository->save($product);
                    }

                    $message = "Product: {$name} | ID: {$id} | coverImage:  {$coverImage} "  ;
                    // Print to console
                    $output->writeln($message); 
                    // Log into system.log
                    $this->logger->info("Product: " . $message);
                } elseif ($isPlaceholder) {
                    $output->writeln("<comment>Product '{$name}' has placeholder cover; skipping.</comment>");
                }
                $count++;
            }

            $output->writeln("<comment>Total products processed: $count</comment>");
            $this->logger->info("Total products processed: " . $count);

            $output->writeln('<info>Done!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        


    }
}
