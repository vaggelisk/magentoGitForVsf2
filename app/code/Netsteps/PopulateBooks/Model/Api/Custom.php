<?php

namespace Netsteps\PopulateBooks\Model\Api;

use Divante\VsbridgeIndexerCore\Elasticsearch\ClientBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Integration\Model\Oauth\Token;
use Psr\Log\LoggerInterface;
use Zend_Log;
use Zend_Log_Writer_Stream;

class Custom
{

    private const URL = 'https://biblionet.gr/wp-admin/admin-ajax.php';

    const CONTEXT_AUTH = 'customer_logged_in';

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



    /**
     * @var CurlFactory
     */
    private CurlFactory $curlFactory;

    protected Context $httpContext;

    protected LoggerInterface $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Token
     */
    protected Token $token;

    public function __construct(
        Token $token,
        Context $httpContext,
        CustomerRepositoryInterface $customerRepository,
        CurlFactory $curlFactory,
        LoggerInterface $logger
      )
      {
          $this->token = $token;
          $this->customerRepository = $customerRepository;
          $this->httpContext = $httpContext;
          $this->curlFactory = $curlFactory;
          $this->logger = $logger;
      }

    protected function getSearchParams()
    {
        $this->postParams["title"] = "γκιακ";
        $this->postParams["contributor"] = "παπαμαρκος";
    }

    /**
     * @inheritdoc
     */
    public function searchBookInBiblionet($value): bool|string
    {
        // Edw tha mporouse na ginei kai to search me vash to isbn
        // alla tha apaitouse kai mia 2h proairetikh metavlhth sth synarthsh
        // opou tha eixe thn timh isbn.
        // An den iphrxe tote tha shmainei oti to search einai sto name

        $response = ['success' => false];
        try {

            $curl = $this->curlFactory->create();
            $curl->addHeader("Content-Type", "application/x-www-form-urlencoded");
            $this->getSearchParams();
            // post method
            $curl->post(self::URL, $this->postParams);

            // output of curl request
            $result = json_decode($curl->getBody(), true);

            $writer = new Zend_Log_Writer_Stream(BP . '/var/log/system.log');
            $logger = new Zend_Log();
            $logger->addWriter($writer);
            $logger->log( print_r($result, 1),1);

            $response = ['success' => true, 'message' => 'kati ' . $this->make_greeklish($value)];

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }
        return json_encode($response);
    }

      /**
       * @inheritdoc
       */
      public function searchBookInOurLibrary($value)
      {
          // Edw tha mporouse na ginei kai to search me vash to isbn
          // alla tha apaitouse kai mia 2h proairetikh metavlhth sth synarthsh
          // opou tha eixe thn timh isbn.
          // An den iphrxe tote tha shmainei oti to search einai sto name

          $response = ['success' => false];
          try {
              // Edw prepei na prosexoume na exoume mono enan index poy na xekinaei me "magento"
              // sthn elasticsearch upodomh
              $client = (new ClientBuilder)->build();
              $indices = $client->cat()->indices(array('index' => 'magento*'));
              $params = [
                  'index' => $indices[0]['index'],
                  'body'  => [
                      'query' => [
                          'match' => [
                              'name' => $value
                          ]
                      ]
                  ]
              ];
              $results = $client->search($params);



              $writer = new Zend_Log_Writer_Stream(BP . '/var/log/system.log');
              $logger = new Zend_Log();
              $logger->addWriter($writer);
              $logger->log(  print_r('einai logged? ' , 1) ,1);


              $response = ['success' => true, 'message' => 'kati ' . $this->make_greeklish($value)];
          } catch (\Exception $e) {
                  $response = ['success' => false, 'message' => $e->getMessage()];
        //                  $this->logger->info($e->getMessage());
          }
          return json_encode($response);
    }



    /**
     * @inheritdoc
     */
    public function createBook($customerId, $title)
    {
        $customer = $this->customerRepository->getById($customerId);
        $groupId = $customer->getGroupId();

        $response = ['success' => false, 'message' => 'user is not authorised to create book'];

        if ($groupId === '4') { // edw '4' einai to groupId bookCreator pou einai to mono pou mporei na dhmiourghsei vivlio
            try {
                // this code snippet is REST Api for create a product
                // it needs to be in different file with clientSearch query
                //
                // gia to sku iparxei avth edw h synarthsh poy mporoume na vasistoume
                // https://gist.github.com/teomaragakis/7580134
                //
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
                $product = $objectManager->create('\Magento\Catalog\Model\Product');
                $product->setSku( $this->make_greeklish( str_replace(' ', '-', $title) ) ); // Set your sku here
                $product->setName($title); // Name of Product
                $product->setAttributeSetId(4); // Attribute set id
                $product->setStatus(1); // Status on product enabled/ disabled 1/0
                $product->setWebsiteIds([1]);
                $product->setWeight(10); // weight of product
                $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $product->setTaxClassId(0); // Tax class id
                $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                $product->setPrice(80); // price of product
                $product->setStockData(
                    array(
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => 1,
                        'qty' => 100
                    )
                );
//                $product->save();

                $writer = new Zend_Log_Writer_Stream(BP . '/var/log/system.log');
                $logger = new Zend_Log();
                $logger->addWriter($writer);
                $logger->log(  print_r('vag' , 1) ,1);

                $response = ['success' => true, 'message' => 'A book with sku '. $this->make_greeklish( str_replace(' ', '-', $title) ) . ' created successfully'];
            } catch (\Exception $e) {
                $response = ['success' => false, 'message' => $e->getMessage()];
                $this->logger->info($e->getMessage());
            }
        }
        return json_encode($response);


    }


    /**
     * Based on http://www.freestuff.gr/forums/viewtopic.php?p=194579#194579
     * @param $text
     * @return array|string|null
     */
    private function make_greeklish($text): array|string|null
    {
        $expressions = array(
            '/[αΑ][ιίΙΊ]/u' => 'e',
            '/[οΟΕε][ιίΙΊ]/u' => 'i',
            '/[αΑ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'af$1',
            '/[αΑ][υύΥΎ]/u' => 'av',
            '/[εΕ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'ef$1',
            '/[εΕ][υύΥΎ]/u' => 'ev',
            '/[οΟ][υύΥΎ]/u' => 'ou',
            '/(^|\s)[μΜ][πΠ]/u' => '$1b',
            '/[μΜ][πΠ](\s|$)/u' => 'b$1',
            '/[μΜ][πΠ]/u' => 'mp',
            '/[νΝ][τΤ]/u' => 'nt',
            '/[τΤ][σΣ]/u' => 'ts',
            '/[τΤ][ζΖ]/u' => 'tz',
            '/[γΓ][γΓ]/u' => 'ng',
            '/[γΓ][κΚ]/u' => 'gk',
            '/[ηΗ][υΥ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'if$1',
            '/[ηΗ][υΥ]/u' => 'iu',
            '/[θΘ]/u' => 'th',
            '/[χΧ]/u' => 'ch',
            '/[ψΨ]/u' => 'ps',
            '/[αά]/u' => 'a',
            '/[βΒ]/u' => 'v',
            '/[γΓ]/u' => 'g',
            '/[δΔ]/u' => 'd',
            '/[εέΕΈ]/u' => 'e',
            '/[ζΖ]/u' => 'z',
            '/[ηήΗΉ]/u' => 'i',
            '/[ιίϊΐΙΊΪ]/u' => 'i',
            '/[κΚ]/u' => 'k',
            '/[λΛ]/u' => 'l',
            '/[μΜ]/u' => 'm',
            '/[νΝ]/u' => 'n',
            '/[ξΞ]/u' => 'x',
            '/[οόΟΌ]/u' => 'o',
            '/[πΠ]/u' => 'p',
            '/[ρΡ]/u' => 'r',
            '/[σςΣ]/u' => 's',
            '/[τΤ]/u' => 't',
            '/[υύϋΥΎΫ]/u' => 'i',
            '/[φΦ]/iu' => 'f',
            '/[ωώ]/iu' => 'o',
        );
        return preg_replace( array_keys($expressions), array_values($expressions), $text);
    }

}
