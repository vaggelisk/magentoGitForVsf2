<?php
/**
 * ProductData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Model\Indexer\Action;

use Divante\VsbridgeIndexerCore\Indexer\RebuildActionInterface;
use Netsteps\MarketplaceVSFbridge\Model\Indexer\Data\ModifierInterface;
use \Netsteps\MarketplaceVSFbridge\Model\Resource\ProductSeller as DataResource;

/**
 * Class ProductData
 * @package Netsteps\MarketplaceVSFbridge\Model\Indexer\Action
 */
class ProductData implements RebuildActionInterface
{
    /**
     * @var DataResource
     */
    private DataResource $_resource;

    /**
     * @var ModifierInterface[]
     */
    private array $_modifiers;

    /**
     * @param DataResource $resource
     * @param array $modifiers
     */
    public function __construct(DataResource $resource, array $modifiers = [])
    {
        $this->_resource = $resource;
        $this->_modifiers = $modifiers;
    }

    /**
     * @inheritDoc
     */
    public function rebuild(int $storeId, array $ids): \Traversable
    {
        $page = 1;

        do {
            $productData = $this->_resource->loadData($ids, null, $page, 1000);
            $page++;

            foreach ($productData as $data){
                foreach ($this->_modifiers as $modifier){
                    $modifier->modify($data);
                }

                if (!isset($data['id'])) {
                    continue;
                }

                yield $data['id'] => $data;
            }
        } while (!empty($productData));
    }
}
