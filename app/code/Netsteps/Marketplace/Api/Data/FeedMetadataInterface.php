<?php
/**
 * FeedMetadataInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Api\Data;

/**
 * Interface FeedMetadataInterface
 * @package Netsteps\Marketplace\Api\Data
 */
interface FeedMetadataInterface
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FAILED = 'failed';
    const STATUS_INVALID = 'invalid';
    const STATUS_SUCCESS = 'success';

    const TYPE_CSV = 'csv';
    const TYPE_XML = 'xml';

    const ALLOWED_XML_TAGS = [
        '?xml',
        '![CDATA[',
        '![CDATA',
        'CDATA',
        'feed',
        'created_at',
        'products',
        'product',
        'created_at',
        'sku',
        'retail_price',
        'price',
        'special_price',
        'stock',
        'qty',
        'is_in_stock',
        'estimated_delivery',
        'weight',
        'size_info',
        'composition_info',
        'name',
        'description',
        'mpn',
        'image',
        'brand',
        'color',
        'categories',
        'products',
        'additional_images',
        'size',
        'length',
        'attributes',
        'variation',
        'variations',
    ];

    /**
     * Get all available status that can a feed has
     * @return string[]
     */
    public function getAvailableStatuses(): array;

    /**
     * Get all available accepted file types
     * @return string[]
     */
    public function getAcceptedFileTypes(): array;

    /**
     * Check if given type is accepter
     * @param string $fileType
     * @return bool
     */
    public function isAcceptedType(string $fileType): bool;
}
