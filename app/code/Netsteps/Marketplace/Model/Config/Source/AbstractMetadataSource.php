<?php
/**
 * AbstractMetadataSource
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface as Metadata;

/**
 * Class AbstractMetadataSource
 * @package Netsteps\Marketplace\Model\Config\Source
 */
abstract class AbstractMetadataSource implements OptionSourceInterface
{
    /**
     * @var Metadata
     */
    protected Metadata $_metadata;

    /**
     * @param Metadata $metadata
     */
    public function __construct(Metadata $metadata)
    {
        $this->_metadata = $metadata;
    }

    /**
     * @param array $values
     * @return array
     */
    protected function transformSingleArrayToOptions(array $values): array {
        $options = [];

        foreach ($values as $value){
            $options[] = [
                'value' => $value,
                'label' => __(ucwords(mb_ereg_replace('_', ' ', $value)))
            ];
        }

        return $options;
    }
}
