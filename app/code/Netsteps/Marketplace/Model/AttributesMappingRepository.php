<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2023 Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model;

use Netsteps\Marketplace\Api\AttributesMappingRepositoryInterface;
use Netsteps\Marketplace\Model\Config\Source\SeasonValuesSource;
use Netsteps\Marketplace\Model\Product\Attribute\Source\EstimatedDelivery;

class AttributesMappingRepository implements AttributesMappingRepositoryInterface
{

    const ATTRIBUTE_SEASON = 'season';
    const ATTRIBUTE_ESTIMATED_DELIVERY = 'estimated_delivery';

    private SeasonValuesSource $seasonSource;

    private EstimatedDelivery $deliverySource;

    /**
     * @param SeasonValuesSource $seasonSource
     * @param EstimatedDelivery $deliverySource
     */
    public function __construct(
        SeasonValuesSource $seasonSource,
        EstimatedDelivery $deliverySource
    )
    {
        $this->seasonSource = $seasonSource;
        $this->deliverySource = $deliverySource;
    }

    /**
     * @inheritDoc
     */
    public function get(string $attributeCode): array
    {
        $result = [];
        switch ($attributeCode){
            case self::ATTRIBUTE_SEASON:
                $result = $this->seasonSource->toOptionArray();
                break;
            case self::ATTRIBUTE_ESTIMATED_DELIVERY:
                $result = $this->deliverySource->getAllOptions(false);
                break;
        }
        return $result;
    }

}
