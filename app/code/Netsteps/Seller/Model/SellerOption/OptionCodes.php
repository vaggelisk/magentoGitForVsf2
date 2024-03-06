<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\SellerOption;

use Netsteps\Seller\Api\Data\SellerOptionInterfaceFactory;

class OptionCodes
{
    private SellerOptionInterfaceFactory $sellerOptionInterfaceFactory;

    /**
     * @param SellerOptionInterfaceFactory $sellerOptionInterfaceFactory
     */
    public function __construct(SellerOptionInterfaceFactory $sellerOptionInterfaceFactory)
    {
        $this->sellerOptionInterfaceFactory = $sellerOptionInterfaceFactory;
    }

    /**
     * @return string[]
     */
    public function getAllSellerOptionsFields(): array
    {
        return [
            'logo',
            'description',
            'website',
            'telephone',
            'street',
            'city',
            'postcode',
            'region',
            'company_name',
            'tax_office',
            'vat_number',
            'profession',
            'gemi_number',
            'started_on'
        ];
    }

    /**
     * @return array
     */
    public function createEmptyOptionFields():array
    {
        $options = [];

        foreach ($this->getAllSellerOptionsFields() as $field){
            /** @var SellerOptionInterface $instance */
            $instance = $this->sellerOptionInterfaceFactory->create();
            $instance->setOptionName($field);
            $instance->setOptionValue(null);
            $instance->setStoreId(0);
            $options[] = $instance;
        }

        return $options;
    }

}
