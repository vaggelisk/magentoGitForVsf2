<?php
/**
 * AttributeArrayMap
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Model\Product\Attribute;


/**
 * Class AttributeArrayMap
 * @package Netsteps\MarketplaceVSFbridge\Model\Product\Attribute
 */
class JsonDecoder
{
    /**
     * @var array
     */
    private array $_attributes;

    /**
     * @var array
     */
    private array $_fieldMapping;

    /**
     * @param array $attributes
     * @param array $fieldMapping
     */
    public function __construct(array $attributes, array $fieldMapping = [])
    {
        $this->_attributes = $attributes;
        $this->_fieldMapping = $fieldMapping;
    }

    /**
     * Decode to array
     * @param array $data
     * @return array
     */
    public function decode(array $data): array {
        foreach ($this->_attributes as $attribute) {
            $value = $data[$attribute] ?? false;

            if (is_string($value)) {
                $decoded = @json_decode($value, true);

                if ($decoded) {
                    if (is_array($decoded)){
                        $decoded = array_map([$this, 'mapFields'], $decoded);
                    }
                    $data[$attribute] = $decoded;
                }
            }
        }

        return $data;
    }

    /**
     * Map fields to handle numeric values
     * @param $value
     * @return mixed
     */
    private function mapFields($value): mixed {
        if (!is_numeric($value)){
            return $value;
        }

        return str_contains($value, '.') ? (float)$value : (int) $value;
    }
}
