<?php
/**
 * AbstractIdentity
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email;

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AbstractIdentity
 * @package Netsteps\MarketplaceSales\Model\Order\Email
 */
class AbstractIdentity implements IdentityInterface
{
    /**
     * @var ScopeConfig
     */
    private ScopeConfig $_scopeConfig;

    /**
     * @var string
     */
    private string $group;

    /**
     * @param ScopeConfig $scopeConfig
     * @param string $group
     */
    public function __construct(
        ScopeConfig $scopeConfig,
        string $group = ''
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->group = $group;
    }

    /**
     * Get config value
     * @param string $path
     * @param int|null $storeId
     * @return string|null
     */
    protected function getConfig(string $path, ?int $storeId): ?string {
        $fullPath = 'marketplace/emails/' . trim($path, '/');
        return $this->_scopeConfig->getValue($fullPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(?int $storeId = null): bool
    {
        if (!$this->hasGroup()){
            return false;
        }
        $path = $this->group . '/enable';
        return (bool)(int) $this->getConfig($path, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getSender(?int $storeId = null): string
    {
        if (!$this->hasGroup()){
            return '';
        }
        $path = $this->group . '/sender';
        return (string)$this->getConfig($path, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(?int $storeId = null): string
    {
        if (!$this->hasGroup()){
            return '';
        }
        $path = $this->group . '/template';
        return (string)$this->getConfig($path, $storeId);
    }

    /**
     * Check if has group
     * @return bool
     */
    protected function hasGroup(): bool {
        return trim($this->group) !== '';
    }

    /**
     * @inheritDoc
     */
    public function getAllowedSellers(?int $storeId = null): array
    {
        if (!$this->hasGroup()){
            return [];
        }
        $path = $this->group . '/allowed_sellers';
        $allowed =  (string)$this->getConfig($path, $storeId);
        return explode(',', $allowed);
    }
}
