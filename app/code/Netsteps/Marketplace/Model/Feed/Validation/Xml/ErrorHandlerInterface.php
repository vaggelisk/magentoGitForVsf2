<?php
/**
 * ErrorHandlerInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Validation\Xml;

use Magento\Framework\DataObject;

/**
 * Interface ErrorHandlerInterface
 * @package Netsteps\Marketplace\Model\Feed\Validation\Xml
 */
interface ErrorHandlerInterface
{
    /**
     * Handle the errors on xml parsing and return if process should raise an exception
     * @param \DOMDocument $document
     * @param \LibXMLError[] $errors
     * @param DataObject $model
     * @return bool
     */
    public function handle(\DOMDocument $document, array $errors, DataObject $model): bool;
}
