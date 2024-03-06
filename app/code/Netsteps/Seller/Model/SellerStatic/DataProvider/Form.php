<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\SellerStatic\DataProvider;

use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Netsteps\Seller\Api\SellerStaticRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\SellerStatic\CollectionFactory;

class Form extends AbstractDataProvider
{

    private Registry $registry;

    /**
     * @var array
     */
    private $loadedData;


    public function __construct(
        Registry $registry,
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $data = $this->registry->registry(SellerStaticRepositoryInterface::REGISTRY_KEY);
        $this->loadedData[$data->getId()]['general'] = $data->getData();
        return $this->loadedData;
    }
}
