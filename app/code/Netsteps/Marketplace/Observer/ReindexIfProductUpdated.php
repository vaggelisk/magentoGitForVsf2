<?php

namespace Netsteps\Marketplace\Observer;

use Divante\VsbridgeIndexerCore\Indexer\Action\AbstractAction;
use Divante\VsbridgeIndexerCore\Indexer\Action\ActionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Netsteps\Marketplace\Model\Product\Attribute\ChildNotVisibleInFrontInterface;
use Netsteps\Seller\Api\SellerRepositoryInterface;

class ReindexIfProductUpdated implements ObserverInterface
{
    /**
     * @var \Divante\VsbridgeIndexerCore\Indexer\Action\AbstractAction
     */
    protected AbstractAction $_indexer;

    /**
     * @var \Netsteps\Marketplace\Model\Product\Attribute\ChildNotVisibleInFrontInterface
     */
    protected ChildNotVisibleInFrontInterface $_childNotVisibleInFront;

    /**
     * @var \Netsteps\Seller\Api\SellerRepositoryInterface
     */
    protected SellerRepositoryInterface $_sellerRepository;

    /**
     * @var string[]
     */
    private array $attributesThatRequireReindex;

    /**
     * @param \Divante\VsbridgeIndexerCore\Indexer\Action\ActionFactory $indexerFactory
     * @param \Netsteps\Marketplace\Model\Product\Attribute\ChildNotVisibleInFrontInterface $childNotVisibleInFront
     * @param \Netsteps\Seller\Api\SellerRepositoryInterface $sellerRepository
     * @param array $attributesThatRequireReindex
     */
    public function __construct(
        ActionFactory $indexerFactory,
        ChildNotVisibleInFrontInterface $childNotVisibleInFront,
        SellerRepositoryInterface $sellerRepository,
        array $attributesThatRequireReindex = []
    )
    {
        $this->_indexer = $indexerFactory->create('rows', 'product');
        $this->_childNotVisibleInFront = $childNotVisibleInFront;
        $this->_sellerRepository = $sellerRepository;
        $this->attributesThatRequireReindex = $attributesThatRequireReindex;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Log_Exception
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $logger = new \Zend_Log(
            new \Zend_Log_Writer_Stream(BP . '/var/log/reindex.log')
        );

        $logger->info("Saving product {$product->getSku()}\n");

        $needsReindex = false;

        foreach ($this->attributesThatRequireReindex as $attributeCode){
            if(
                $product->getOrigData($attributeCode) != $product->getData($attributeCode)
            )
            {
                $logger->info("Product attribute {$attributeCode} has changed...reindexing...\n");
                $needsReindex = true;
                break;
            }
        }

        $isVisibleInFrontChanged = false;

        if($product->getOrigData('is_visible_in_front') != $product->getData('is_visible_in_front')){
            $isVisibleInFrontChanged = true;
        }

        if(
            $isVisibleInFrontChanged &&
            ($product->getData('is_visible_in_front') !== 1) &&
            ($product->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE)
        ){
            $sellerSource = $this->getSellerSourceFromProduct($product);
            $this->_childNotVisibleInFront->setChildAsNotVisibleInFront([$product], $sellerSource);
            $needsReindex = true;
        }

        if($needsReindex){
            $this->_indexer->execute([$product->getId()]);
        }
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    protected function getSellerSourceFromProduct(ProductInterface $product): string
    {
        $sellerId = $product->getData('ns_distributor');

        if(empty($sellerId)){
            return 'all';
        }

        $sellerId = (int)$sellerId;

        try{
            $seller = $this->_sellerRepository->getById($sellerId);
            return $seller->getSourceCode();
        }
        catch (NoSuchEntityException $e){
            return 'all';
        }
    }
}
