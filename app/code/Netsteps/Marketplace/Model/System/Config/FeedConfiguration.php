<?php
/**
 * FeedConfiguration
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\System\Config;

/**
 * Class FeedConfiguration
 * @package Netsteps\Marketplace\Model\System\Config
 */
class FeedConfiguration extends AbstractConfiguration implements FeedConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getSellersForFeedNormalization(?int $storeId = null): array
    {
        $sellers = $this->getConfig(self::FIELD_SELLER_NORMALIZED, $storeId);
        return is_null($sellers) ? [] : array_map([$this, '_castToInt'], explode(',', (string)$sellers));
    }

    /**
     * Cast a string variable to int
     * @param string $value
     * @return int
     */
    private function _castToInt(string $value): int {
        return (int)$value;
    }
}
