<?php
/**
 * Master
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Validation\Xml\Error\Handler;

use Magento\Framework\DataObject;
use Netsteps\Marketplace\Model\Feed\Validation\Xml\ErrorHandlerInterface;

/**
 * Class Master
 * @package Netsteps\Marketplace\Model\Feed\Validation\Xml\Error\Handler
 */
class Master implements ErrorHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function handle(\DOMDocument $document, array $errors, DataObject $model): bool
    {
        if (empty($errors)) {
            return false;
        }

        $productsElements = $document->getElementsByTagName('product');

        if (count($productsElements) === 0){
            return true;
        }

        $products = [];
        $skuToAvoid = [];

        $errorLines = array_map(function ($error){
            return $error->line;
        }, $errors);

        /** @var  $product \DOMElement */
        foreach ($productsElements as $product) {
            $products[$product->getLineNo()] = (string)$product->getElementsByTagName('sku')
                ->item(0)->nodeValue;
        }

        $productLines = array_values(array_keys($products));
        $maxIndex = count($productLines) - 1;
        $maxLine = max($productLines);

        foreach ($errorLines as $errorLine){
            if (isset($products[$errorLine])) {
                $skuToAvoid[] = $products[$errorLine];
            } else {
                $key = null;
                foreach ($productLines as $index => $productLine) {
                    if ($errorLine < $productLine) {
                        $key = $index === $maxIndex ? $index : $index - 1;
                        break;
                    }
                }

                if (!is_null($key)) {
                    $skuToAvoid[] = $products[$productLines[$key]];
                } elseif ($errorLine > $maxLine) {
                    $skuToAvoid[] = $products[$productLines[$maxIndex]];
                }
            }
        }

        if (!empty($skuToAvoid)) {
            $model->setData('invalid_sku', array_unique($skuToAvoid));
        }

        return false;
    }
}
