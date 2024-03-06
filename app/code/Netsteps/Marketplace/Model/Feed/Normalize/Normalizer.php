<?php
/**
 * Normalizer
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Normalize;

use Netsteps\Marketplace\Api\Data\FeedInterface;
use Netsteps\Marketplace\Model\System\Config\FeedConfigurationInterface as Config;

/**
 * Class Normalizer
 * @package Netsteps\Marketplace\Model\Feed\Normalize
 */
class Normalizer implements NormalizerInterface
{

    /**
     * @var AdapterInterface[]
     */
    private array $_adapters;

    /**
     * @var Config
     */
    private Config $_config;

    /**
     * @param Config $config
     * @param AdapterInterface[] $adapters
     */
    public function __construct(Config $config, array $adapters = []) {
        $this->_config = $config;
        $this->_adapters = $adapters;
    }

    /**
     * @inheritDoc
     */
    public function normalize(FeedInterface $feed, ?int $storeId = null): void
    {
        $sellers = $this->_config->getSellersForFeedNormalization($storeId);

        if (empty($sellers) || !in_array($feed->getSellerId(), $sellers)){
            return;
        }

        foreach ($this->_adapters as $adapter){
            $adapter->execute($feed);
        }
    }
}
