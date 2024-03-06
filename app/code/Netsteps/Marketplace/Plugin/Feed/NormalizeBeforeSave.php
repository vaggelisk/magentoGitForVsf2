<?php
/**
 * NormalizeBeforeSave
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Plugin\Feed;
use Netsteps\Marketplace\Model\Feed\Normalize\NormalizerInterface;

/**
 * Class NormalizeBeforeSave
 * @package Netsteps\Marketplace\Plugin\Feed
 */
class NormalizeBeforeSave
{
    /**
     * @var NormalizerInterface
     */
    private NormalizerInterface $_normalizer;

    /**
     * @param NormalizerInterface $normalizer
     */
    public function __construct(NormalizerInterface $normalizer)
    {
        $this->_normalizer = $normalizer;
    }

    /**
     * Normalize feed before save
     * @param \Netsteps\Marketplace\Model\ResourceModel\Feed $feedResource
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @return \Netsteps\Marketplace\Api\Data\FeedInterface[]
     */
    public function beforeSave(
        \Netsteps\Marketplace\Model\ResourceModel\Feed $feedResource,
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed
    ): array {
        $this->_normalizer->normalize($feed);
        return [$feed];
    }
}
