<?php
/**
 * Data
 *
 * @copyright Copyright © 2020 Kostas Tsiapalis. All rights reserved.
 * @author    k.tsiapalis86@gmail.com
 */

namespace Netsteps\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\ScopeInterface as Scope;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Variable\Model\Variable;
use Magento\Catalog\Model\ProductRepository;

/**
 * @package Netsteps\Base\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var StoreManager
     */
    protected $_storeManager;

    /**
     * @var ThemeProviderInterface
     */
    protected $_themeProvider;

    /**
     * @var DesignInterface
     */
    protected $_design;

    /**
     * @var Variable
     */
    protected $_variable;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManager $storeManager
     * @param ThemeProviderInterface $themeProvider
     * @param DesignInterface $design
     * @param Variable $variable
     * @param ProductRepository $productRepository
     */
    public function __construct(Context $context, StoreManager $storeManager, ThemeProviderInterface $themeProvider, DesignInterface $design, Variable $variable, ProductRepository $productRepository)
    {
        $this->_storeManager = $storeManager;
        $this->_themeProvider = $themeProvider;
        $this->_design = $design;
        $this->_variable = $variable;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Get Media Url
     * @param string $path
     * @return string
     */
    public function getMediaUrl(string $path = ''): string {
        $mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        return $mediaUrl . $path;
    }

    /**
     * Get Netsteps Logo Url
     * @deprecated Uses getviewfileurl and the logo is now on web/images/logo.svg on netsteps_base
     * @return string
     */
    public function getNsLogoUrl(): string {
        return $this->getMediaUrl('netsteps/logo.svg');
    }

    /**
     * Get Url based in given path
     * @param string $path
     * @return string
     */
    public function getUrl(string $path = '')
    {
        return $this->getStore()->getBaseUrl() . $path;
    }

    /**
     * Get current Store
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore():  \Magento\Store\Api\Data\StoreInterface{
        return $this->_storeManager->getStore();
    }

    /**
     * Get a module configuration
     * @param string $path
     * @param int|null $storeId
     * @return string|null
     */
    public function getModuleConfig(string $path, ?int $storeId = null): ?string {
        return $this->scopeConfig->getValue($path, Scope::SCOPE_STORE, $storeId);
    }

    /**
     * @return string
     */
    public function getPageIdentifier(): ?string {
        return $this->_getRequest()->getFullActionName();
    }

    /**
     * Get current theme code
     * @param bool $useCode
     * @return string
     */
    public function getThemeCode(bool $useCode = true): ?string {
        $designParams = $this->_design->getDesignParams();

        if(isset($designParams['themeModel']) && $designParams['themeModel'] instanceof \Magento\Theme\Model\Theme){
            return $useCode ? $designParams['themeModel']->getCode() : $designParams['themeModel']->getId();
        }

        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->_themeProvider->getThemeById($themeId);

        return $useCode ? $theme->getCode() : $theme->getId();
    }

    /**
     * @return int|string
     */
    public function getFrontBaseUrl(){
        $frontBaseUrl = $this->_variable->loadByCode('base_url_front', $this->getStore());
        if($frontBaseUrl->getPlainValue()){
            return $frontBaseUrl->getPlainValue().$this->getStore()->getCode()."/";
        }
        return 0;
    }

    /**
     * Get product by id
     * @param int $productId
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProductById(int $productId): ?\Magento\Catalog\Model\Product{
        try {
            return $this->_productRepository->getById($productId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
