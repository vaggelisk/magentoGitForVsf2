<?php
/**
 * AbstractConfiguration
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\App\ProductMetadataInterface as ProductMetadata;

/**
 * Class AbstractConfiguration
 * @package Netsteps\Marketplace\Model\System\Config
 */
class AbstractConfiguration
{
    /**
     * @var ScopeConfig
     */
    protected ScopeConfig $_scopeConfig;

    /**
     * @var StoreManager
     */
    protected StoreManager $_storeManager;

    /**
     * @var LocaleResolver
     */
    private LocaleResolver $_localeResolver;

    /**
     * @var ProductMetadata
     */
    private ProductMetadata $_productMetadata;

    /**
     * @var string
     */
    private string $_groupPath;

    /**
     * @param ScopeConfig $scopeConfig
     * @param StoreManager $storeManager
     * @param LocaleResolver $localeResolver
     * @param ProductMetadata $productMetadata
     * @param string $groupPath
     */
    public function __construct(
        ScopeConfig     $scopeConfig,
        StoreManager    $storeManager,
        LocaleResolver  $localeResolver,
        ProductMetadata $productMetadata,
        string $groupPath
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_productMetadata = $productMetadata;
        $this->_groupPath = trim($groupPath, '/');
    }

    /**
     * Get config for given group path
     * @param string $field
     * @param int|null $storeId
     * @return string|null
     */
    protected function getConfig(string $field, ?int $storeId = null): ?string {
        if(!$this->_groupPath) {
            return null;
        }

        $path = $this->_groupPath . '/' . trim($field, '/');

        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check if is enterprise edition
     * @return bool
     */
    public function isEnterpriseEdition(): bool {
        return $this->_productMetadata->getEdition() === 'Enterprise';
    }

    /**
     * Get current locale
     * @return string
     */
    public function getLocale(): string {
        return $this->_localeResolver->getLocale();
    }
}
