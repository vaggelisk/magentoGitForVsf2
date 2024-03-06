<?php
/**
 * DataExtractTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Traits\Feed;

use Netsteps\Marketplace\Api\Data\FeedInterface;

/**
 * Trait DataExtractTrait
 * @package Netsteps\Marketplace\Traits\Feed
 */
trait DataExtractTrait
{
    /**
     * Extract Errors from Feed
     * @param FeedInterface $feed
     * @param array $excludedTypes
     * @return array
     */
    protected function extractErrors(FeedInterface $feed, array $excludedTypes = []): array {
        $additionalInfo = $feed->getAdditionalInfo();

        if (!$additionalInfo){
            return [];
        }

        $errors = [];
        $errorsRaw = @json_decode($additionalInfo, true);

        if (!is_array($errorsRaw)){
            return [];
        }

        foreach ($errorsRaw as $key => $errorList) {
            if (in_array($key, $excludedTypes)){
                continue;
            }

            $errors[] = [
                'key' => $key,
                'title' => __(ucwords(str_replace('_', ' ', $key))),
                'errors' => $errorList
            ];
        }

        return $errors;
    }
}
