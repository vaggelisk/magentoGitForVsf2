<?php
/**
 * EncryptionTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Traits;

/**
 * Trait EncryptionTrait
 * @package Netsteps\Marketplace\Traits
 */
trait EncryptionTrait
{
    /**
     * Create hash key from an array of data
     * @param array $data
     * @return string
     */
    protected function createHash(array $data): string {
        ksort($data);
        return md5(json_encode($data));
    }
}
