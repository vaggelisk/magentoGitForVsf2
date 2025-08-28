<?php
 
namespace Binstellar\CategoryThumb\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;


class GetCatInfo extends Template
{
    protected $_storeManager;
    protected $categoryCollectionFactory;
    
    public function __construct(
        Context $context, 
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

     public function getImgUrl()
    {
        $mediaUrl = $this->_storeManager
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $ImgUrl = $mediaUrl.'catalog/category/';
        return $ImgUrl;
    }

    public function getCategoryCollection()
    {
       $collection = $this->categoryCollectionFactory->create()
          ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_display_on_homepage', '1');  
        return $collection;
    }
}
