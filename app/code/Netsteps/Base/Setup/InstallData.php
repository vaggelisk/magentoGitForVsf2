<?php

/**
 * @Author Vasilis Neris
 * @description: Adds default data to the store. If you have the store data before you start the project set them here.
 * @note if you see plain html in variable. Sorry :) I will change it in the future. Had no time
 */

namespace Netsteps\Base\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\State;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\Store;
use Magento\Theme\Model\Config;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\Rule;

//TODO Refactor code to use interface. BlockFactory uses deprecated methods

class InstallData implements InstallDataInterface
{
    const THEME_NAME = 'Netsteps/default';

    private $blockFactory;

    private $configFactory;

    private $configWriter;

    private $scopeConfig;

    private $config;

    private $collectionFactory;

    private $pageFactory;

    private $taxRate;

    private $taxRule;


    public function __construct(
        BlockFactory $blockFactory,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig,
        Factory $configFactory,
        State $state,
        CollectionFactory $collectionFactory,
        Config $config,
        PageFactory $pageFactory,
        Rate $taxRate,
        Rule $taxRule

    ) {
        $this->blockFactory      = $blockFactory;
        $this->configWriter      = $configWriter;
        $this->scopeConfig       = $scopeConfig;
        $this->configFactory     = $configFactory;
        $this->collectionFactory = $collectionFactory;
        $this->config            = $config;
        $this->pageFactory       = $pageFactory;
        $this->taxRate           = $taxRate;
        $this->taxRule           = $taxRule;

        $state->setAreaCode('adminhtml');
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->setDefaultStoreValues();
//        $this->setDefaultSocials();
//        $this->setFooter();
        $this->setCopyright();
        $this->setCoreConfigTaxSettings();
        $this->setTaxRates();
        $this->setImageSettings();
        $this->setNoRoute();
        $this->setTheme();


        $setup->endSetup();
    }

    protected function setFooter()
    {
        $footerColumnFirstContent = '<h5>Εξυπηρετηση <span class="accordion-toggler icon-right-chevron"></span></h5>
                                    <ul>
                                            <li><a href="#">Συχνες ερωτήσεις</a></li>
                                            <li><a href="#">Τρόποι πληρωμής</a></li>
                                            <li><a href="#">Τρόποι αποστολής</a></li>
                                            <li><a href="#">Οικονομικά αποτελέσματα</a></li>
                                            <li><a href="#">Όροι χρήσης</a></li>
                                            <li><a href="#">GDPR tools</a></li>
                                    </ul>';

        $footerColumnSecondContent = '<h5>Εταιρια <span class="accordion-toggler icon-right-chevron"></span></h5>
                                        <ul>
                                            <li><a href="#">Ποιοι ειμαστε</a></li>
                                            <li><a href="#">Επικοινωνια</a></li>
                                            <li>Τηλ: {{config path="general/store_information/phone"}}</li>
                                            <li><a href="#">Οικονομικά αποτελέσματα</a></li>
                                            <li><a href="#">Όροι χρήσης</a></li>
                                            <li><a href="#">GDPR tools</a></li>
                                        </ul>';

        $cmsBlocks = [
            'footerColumn1' => [
                'title'      => 'Footer Column First',
                'identifier' => 'footer-column-1',
                'content'    => $footerColumnFirstContent,
                'is_active'  => 1,
                'stores'     => [0],
                'sort_order' => 0
            ],
            'footerColumn2' => [
                'title'      => 'Footer Column Second',
                'identifier' => 'footer-column-2',
                'content'    => $footerColumnSecondContent,
                'is_active'  => 1,
                'stores'     => [0],
                'sort_order' => 0
            ]
        ];

        foreach ($cmsBlocks as $block) {
            $mageBlock = $this->blockFactory->create()->load($block['identifier'], 'identifier');
            if (!$mageBlock->getId()) {
                $this->blockFactory->create()->setData($block)->save();
            }
        }
    }

    /**
     * Set some default usps for header and block
     */
    protected function setDefaultUsps()
    {
        $path = 'topbar/general/usps';

        if ($this->scopeConfig->getValue($path) == '' || $this->scopeConfig->getValue($path) == '[]') {
            $index = time();
            $configData = [
                'section' => 'topbar',
                'website' => null,
                'store'   => null,
                'groups'  => [
                    'general' => [
                        'fields' => [
                            'usps' => [
                                'value' => [
                                    $index . '_0' => ["text" => "Άμεση Παράδοση σε 2 - 3 εργάσιμες ημέρες"],
                                    $index . '_1' => ["text" => "Δωρεάν αποστολή σε όλη την Ελλάδα "],
                                    $index . '_2' => ["text" => "Όλοι οι τρόποι πληρωμής"]
                                ],
                            ],
                        ],
                    ],
                ],
            ];
            $configModel = $this->configFactory->create(['data' => $configData]);
            $configModel->save();
        }
    }

    // guide https://nwdthemes.com/2018/06/21/magento-2-working-with-arrayserialized-backend-model/
    protected function setDefaultSocials()
    {
        $path = 'socials/additional/social';

        if ($this->scopeConfig->getValue($path) == '' || $this->scopeConfig->getValue($path) == '[]') {
            $index = time();
            $configData = [
                'section' => 'socials',
                'website' => null,
                'store'   => null,
                'groups'  => [
                    'additional' => [
                        'fields' => [
                            'social' => [
                                'value' => [
                                    $index . '_0' => ["title" => "facebook", "icon" => "icon-facebook", 'link' => '#'],
                                    $index . '_1' => ["title" => "twitter", "icon" => "icon-twitter", 'link' => '#'],
                                    $index . '_2' => ["title" => "instagram", "icon" => "icon-instagram", 'link' => '#'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $configModel = $this->configFactory->create(['data' => $configData]);
            $configModel->save();
        }
    }

    protected function setDefaultStoreValues()
    {
        $generalStoreInformation = [
                [
                    'path'  => 'general/store_information/name',
                    'value' => 'Netsteps'
                ],
                [
                    'path'  => 'general/store_information/phone',
                    'value' => '210 6011281'
                ],
                [
                    'path'  => 'general/store_information/city',
                    'value' => 'Gerakas'
                ],
                [
                    'path'  => 'general/store_information/street_line1',
                    'value' => 'Eth. Antistaseos 19'
                ],
                [
                    'path'  => 'general/store_information/postcode',
                    'value' => '153 44'
                ],
                [
                    'path'  => 'general/store_information/hours',
                    'value' => 'ΔΕΥ − ΠΑΡ: 8.00 πμ − 5.00 μμ ∕ ΣΑΒ: 9.00 πμ − 3.00 μμ'
                ],
                [
                    'path'  => 'general/store_information/region_id',
                    'value' => 'Attiki'
                ],
            ];
        foreach ($generalStoreInformation as $info) {
            if ($this->scopeConfig->getValue($info['path']) == '') {
                $this->configWriter->save($info['path'], $info['value']);
            }
        }
    }

    /**
     * Assign Theme
     *
     * @return void
     */
    protected function setTheme()
    {
        $themes = $this->collectionFactory->create()->loadRegisteredThemes();


        /**
         * @var \Magento\Theme\Model\Theme $theme
         */
        foreach ($themes as $theme) {

            if ($theme->getCode() == self::THEME_NAME) {
                $this->config->assignToStore(
                    $theme,
                    [Store::DEFAULT_STORE_ID],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT
                );
            }
        }
    }

    protected function setCopyright()
    {
        $path = 'design/footer/copyright';
        $copyright = $this->scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        if (strpos($copyright, '[STORE NAME]') == false) {
            $copyrightContent = 'Copyright © <span id="this-year"></span> [STORE NAME] All Rights Reserved
                                <script>
                                    let today = new Date();
                                    let year = today.getFullYear();
                                    document.getElementById(\'this-year\').innerHTML = year;
                                </script>';

            $this->configWriter->save($path, $copyrightContent, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        }
    }

    protected function setNoRoute()
    {
        $content = '{{block class="Magento\Framework\View\Element\Template" area="home" name="no-route" template="Netsteps_Base::theme/no-route.phtml"}}';
        $page = $this->pageFactory->create()->load(
            'no-route',
            'identifier'
        );
        if ($page->getId()) {
            $page->setContentHeading('');
            $page->setPageLayout('1column');
            $page->setContent($content);
            $page->save();
        }
    }

    protected function setCoreConfigTaxSettings(){


                $taxCalculation = [
                    [
                        'path'  => 'tax/calculation/based_on',
                        'value' => 'origin'
                    ],
                    [
                        'path'  => 'tax/calculation/price_includes_tax',
                        'value' => '1'
                    ],
                    [
                        'path'  => 'tax/calculation/shipping_includes_tax',
                        'value' => '1'
                    ],
                    [
                        'path'  => 'tax/defaults/country',
                        'value' => 'GR'
                    ],
                    [
                        'path'  => 'tax/display/type',
                        'value' => '2'
                    ],
                    [
                        'path'  => 'tax/cart_display/price',
                        'value' => '2'
                    ],
                    [
                        'path'  => 'tax/cart_display/subtotal',
                        'value' => '2'
                    ],
                    [
                        'path'  => 'tax/cart_display/shipping',
                        'value' => '2'
                    ],
                    [
                        'path'  => 'tax/sales_display/price',
                        'value' => '2'
                    ],
                    [
                        'path'  => 'tax/sales_display/subtotal',
                        'value' => '2'
                    ],
                    [
                        'path'  => 'tax/sales_display/shipping',
                        'value' => '2'
                    ],
                    [
                        'path'  => 'shipping/origin/country_id',
                        'value' => 'GR'
                    ],
                    [
                        'path'  => 'shipping/origin/postcode',
                        'value' => '*'
                    ],
                    [
                        'path'  => 'shipping/origin/region_id',
                        'value' => '*'
                    ],

                ];


                foreach ($taxCalculation as $info) {
                    $this->configWriter->save($info['path'], $info['value']);
                }


    }


    private function setImageSettings()
    {

        $imageSettings = [
            [
                'path'  => 'dev/image/default_adapter',
                'value' => 'IMAGEMAGICK'
            ],
            [
                'path'  => 'system/upload_configuration/jpeg_quality',
                'value' => 100
            ],

        ];


        foreach ($imageSettings as $info) {
            $this->configWriter->save($info['path'], $info['value']);
        }
    }


    private function setTaxRates(){

        $this->taxRate->setCode('Greece');
        $this->taxRate->setTaxCountryId('GR');
        $this->taxRate->setRate('24');
        $this->taxRate->setTaxPostcode('*');
        $this->taxRate->save();

        $this->setTaxeRules($this->taxRate->getId());


    }

    private function setTaxeRules($id){

        $this->taxRule->setCode('Greece');
        $this->taxRule->setTaxRateIds([$id]);
        $this->taxRule->setPriority(0);
        $this->taxRule->setCustomerTaxClassIds([3]);
        $this->taxRule->setProductTaxClassIds([2]);
        $this->taxRule->save();

    }
}
