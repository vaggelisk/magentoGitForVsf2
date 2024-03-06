<?php


namespace Netsteps\PopulateBooks\Console;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
//use Magento\Framework\App\Area;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;
use Zend_Log;
use Zend_Log_Writer_Stream;


class Populate extends Command
{
    private const URL = 'https://biblionet.gr/wp-admin/admin-ajax.php';

    /**
     * @var string|null
     */
    private $pathDir;

    /**
     * @var CurlFactory
     */
    private CurlFactory $curlFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    protected DirectoryList $directoryList;

    /**
     * @var array
     */
    private array $postParams = array(
        "action" => "return_detailed_search",
        "contributorkind" => "1",
        "titlekind" => "0",
        "subject" => "0",
        "availability" => "0",
        "origlang" => "0",
        "publisher" => "",
        "series" => "",
        "isbn" => "",
        "yearfrom" => "2000",
        "yearto" => "2024",
        "page" => "1",
        "page_titles" => "21",
        "order" => "td",
    );

    private ProductRepositoryInterface $productRepository;

    /**
     * @param CurlFactory $curlFactory
     * @param LoggerInterface $logger
     * @param DirectoryList $directoryList
     * @param ProductRepositoryInterface $productRepository
     * @@param State $appState
     */
    public function __construct(
        State $appState,
        LoggerInterface $logger,
        CurlFactory $curlFactory,
        DirectoryList $directoryList,
        ProductRepositoryInterface $productRepository
    ) {
        $this->logger = $logger;
        $this->curlFactory = $curlFactory;
        $this->directoryList = $directoryList;
        $this->productRepository = $productRepository;
        $this->appState = $appState;
        parent::__construct();
    }

    /**
     * @var State
     */
    private State $appState;

    protected function configure()
    {
        $this->setName('example:examplePopulateBooks');
        $this->setDescription('Demo command line');
        parent::configure();
    }

    protected function getSearchParams()
    {
        $this->postParams["title"] = "γκιακ";
        $this->postParams["contributor"] = "παπαμαρκος";
    }

    /*
     * @param $pathDir
     */
    protected function getAndSaveImage( $_pathDir )
    {
        // doyleyei mpompa
        // to mono poy prepei na ginei einai na ginei pio abstract
        $image_url = 'https://biblionet.gr/wp-content/uploadsTitleImages/25/b248181.jpg';
        $image_file = $_pathDir.'/b248181.jpg';


//        print_r($directoryList->getRoot());

        $fp = fopen ($image_file, 'w+');              // open file handle

        $ch = curl_init($image_url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // enable if you want
        curl_setopt($ch, CURLOPT_FILE, $fp);          // output to file
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        // curl_setopt($ch, CURLOPT_VERBOSE, true);   // Enable this line to see debug prints
        curl_exec($ch);

        curl_close($ch);                              // closing curl handle
        fclose($fp);                                  // closing file handle
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws StateException
     * @throws InputException
     */
    protected function addExtraAttributes($_pathDir)
    {
        // επόμενος στόχος να  δείχνει ta extra attribute
        // σε όλα τα προϊόντα.
        // Έχει να κάνει με το attribute_Set
        $productId = 2041;
//        $productId = 1;
        $attributeCode = 'writer';
        $value = 'Παπαμάρκος Δημοσθένης Αρβανίτης';
        $storeId = 1;

        $product = $this->productRepository->getById($productId);
        print_r( $product->getImage() );
        // Tromeros tropos gia na machtareis
        // arxeio eikonas se product
        $product->addImageToMediaGallery($_pathDir.'/catalog/product/b/2/b248181.jpg', array('image', 'small_image', 'thumbnail'), true, false);

//       if you want to save new value then follow this code
//        $product->setCustomAttribute($attributeCode, $value);
//      if you want to update the value then follow this code
        $product->addAttributeUpdate($attributeCode, $value, $storeId);
        $this->productRepository->save($product);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(Area::AREA_FRONTEND); // avto einai gia na  mh xtypaei kati
        {
            try {
                $curl = $this->curlFactory->create();
                $curl->addHeader("Content-Type", "application/x-www-form-urlencoded");
                $this->getSearchParams();
                // post method
                $curl->post(self::URL, $this->postParams);

                // output of curl request
                $result = json_decode($curl->getBody(), true);

                $pathDir = $this->directoryList->getPath('media');
                print_r( $pathDir );

                $writer = new Zend_Log_Writer_Stream(BP . '/var/log/system.log');
                $logger = new Zend_Log();
                $logger->addWriter($writer);
                $logger->log("geia sas",2);

                $this->getAndSaveImage( $pathDir );
//                print_r($result[0][1]);
                $output->writeln(' ');
                foreach ($result[0][1] as $key => $value) {
                    $output->writeln( $key." : ". $value );
                }
                $this->addExtraAttributes( $pathDir );
            } catch (Exception $e) {
                $output->writeln($e->getMessage());
                return false;
            }
        }

        $output->writeln("Bye Bye 2");
    }
}
