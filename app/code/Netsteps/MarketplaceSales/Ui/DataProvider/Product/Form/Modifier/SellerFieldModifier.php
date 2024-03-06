<?php
/**
 * SellerFieldModifier
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Ui\DataProvider\Product\Form\Modifier;


use Magento\Framework\Stdlib\ArrayManager as ArrayManager;
use Netsteps\MarketplaceSales\Api\ProductManagementInterface;

/**
 * Class SellerFieldModifier
 * @package Netsteps\MarketplaceSales\Ui\DataProvider\Product\Form\Modifier
 */
class SellerFieldModifier extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private ArrayManager $_arrayManager;

    /**
     * @var array
     */
    private array $_fieldConfig = [
        ProductManagementInterface::LOWEST_SELLER_ID => [
            'disabled' => true
        ],
        ProductManagementInterface::LOWEST_SELLER_DATA => [
            'visible' => false
        ],
        ProductManagementInterface::SELLER_DISCOUNT => [
            'disabled' => true
        ]
    ];

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->_arrayManager = $arrayManager;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta): array
    {
        foreach ($this->_fieldConfig as $fieldName => $additionalConfig) {
            $path = $this->_arrayManager->findPath($fieldName, $meta);
            if (!$path){
                continue;
            }

            $meta = $this->_arrayManager->merge(
                $path . '/arguments/data/config',
                $meta,
                $additionalConfig
            );
        }

        return $meta;
    }
}
