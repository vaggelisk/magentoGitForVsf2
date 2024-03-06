<?php
/**
 * Product
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Index\Mapping;

use Divante\VsbridgeIndexerCore\Api\Mapping\FieldInterface;
use Divante\VsbridgeIndexerCore\Api\MappingInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * Class Product
 * @package Netsteps\MarketplaceVSFbridge\Index\Mapping
 */
class Product implements MappingInterface
{

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * CmsBlock constructor.
     *
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function getMappingProperties()
    {
        $properties = [
            'id' => ['type' => FieldInterface::TYPE_KEYWORD],
            'product_id' => ['type' => FieldInterface::TYPE_LONG],
            'parent_id' => ['type' => FieldInterface::TYPE_LONG],
            'price' => ['type' => FieldInterface::TYPE_DOUBLE],
            'special_price' => ['type' => FieldInterface::TYPE_DOUBLE],
            'final_price' => ['type' => FieldInterface::TYPE_DOUBLE],
            'delivery_id' => ['type' => FieldInterface::TYPE_INTEGER],
            'seller_id' => ['type' => FieldInterface::TYPE_LONG],
            'seller_name' => ['type' => FieldInterface::TYPE_KEYWORD],
            'seller_group' => ['type' => FieldInterface::TYPE_KEYWORD],
            'qty' => ['type' => FieldInterface::TYPE_INTEGER],
            'is_in_stock' => ['type' => FieldInterface::TYPE_BOOLEAN]
        ];

        $mappingObject = new \Magento\Framework\DataObject();
        $mappingObject->setData('properties', $properties);

        $this->eventManager->dispatch(
            'elasticsearch_marketplace_product_mapping_properties',
            ['mapping' => $mappingObject]
        );

        return $mappingObject->getData();
    }
}
