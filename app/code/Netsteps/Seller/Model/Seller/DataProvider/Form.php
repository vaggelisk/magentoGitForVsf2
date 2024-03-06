<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Netsteps\Seller\Api\Data\SellerInterface;
use Netsteps\Seller\Api\Data\SellerStaticInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;
use Netsteps\Seller\Api\SellerStaticRepositoryInterface;
use Netsteps\Seller\Model\ResourceModel\Seller\CollectionFactory;

class Form extends AbstractDataProvider
{

    private SellerRepositoryInterface $sellerRepository;

    private SellerStaticRepositoryInterface $sellerStaticRepository;

    private Registry $registry;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @param SellerRepositoryInterface $sellerRepository
     * @param SellerStaticRepositoryInterface $sellerStaticRepository
     * @param Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        SellerRepositoryInterface $sellerRepository,
        SellerStaticRepositoryInterface $sellerStaticRepository,
        Registry $registry,
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    )
    {
        $this->sellerRepository = $sellerRepository;
        $this->registry = $registry;
        $this->collection = $collectionFactory->create();
        $this->sellerStaticRepository = $sellerStaticRepository;
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
        $data = $this->registry->registry(SellerRepositoryInterface::REGISTRY_KEY);
        $this->loadedData[$data->getId()]['seller']['general'] = $data->getData();
        $this->attachOptions($data);
        $this->attachFeeds($data);
        $this->attachStaticData($data);
        return $this->loadedData;
    }

    /**
     * @param SellerInterface $data
     * @return void
     */
    private function attachOptions($data)
    {
        if($data->getOptions()){
            foreach ($data->getOptions() as $option){
                if($option->getOptionName() == 'logo' && $option->getOptionValue()) {
                    $this->loadedData[$data->getId()]['seller']['options'][$option->getOptionName()][] = [
                        'url' => $option->getOptionValue(),
                        'previewType' => 'image',
                        'type' => 'image/png',
                        'size' => '12'
                    ];

                } else {
                    $this->loadedData[$data->getId()]['seller']['options'][$option->getOptionName()] = $option->getOptionValue();
                }
            }
        }
    }

    /**
     * @param SellerInterface $data
     * @return void
     */
    private function attachFeeds($data)
    {
        if($data->getFeeds()){
            foreach ($data->getFeeds() as $feed){
                $this->loadedData[$data->getId()]['seller']['feeds'][$feed->getType()] = $feed->getUrlPath();
            }
        }
    }

    /**
     * @param SellerInterface $data
     * @return void
     */
    private function attachStaticData($data){
        if($data->getEntityId()){
            try {
                $staticData = $this->sellerStaticRepository->getBySellerId($data->getEntityId());
                $this->loadedData[$data->getEntityId()]['seller']['static'][SellerStaticInterface::IBAN] = $staticData->getIban();
                $this->loadedData[$data->getEntityId()]['seller']['static'][SellerStaticInterface::BENEFICIARY] = $staticData->getBeneficiary();
                $this->loadedData[$data->getEntityId()]['seller']['static'][SellerStaticInterface::COMMISSION] = $staticData->getCommission();
            } catch (NoSuchEntityException $e) {
            }
        }
    }



}
