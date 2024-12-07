<?php
namespace Netsteps\PopulateBooks\Model\Resolver\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

use Psr\Log\LoggerInterface;
use Zend_Log;
use Zend_Log_Writer_Stream;

class GetSubtitle implements ResolverInterface
{
    protected Product $productdata;

    public function __construct(
        Product $productdata
    ) {
        $this->productdata = $productdata;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $product = $value['model'];
        $productdata = $this->productdata->load($product->getId());

//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
//        $product = $objectManager->create('\Magento\Catalog\Model\Product');
//        return $product->getName();
        $result = $productdata->getCustomAttribute('subtitle')->getValue();

        $writer = new Zend_Log_Writer_Stream(BP . '/var/log/system.log');
        $logger = new Zend_Log();
        $logger->addWriter($writer);
        $logger->log( print_r( gettype($result), 1),1);
        $logger->log( print_r( $result, 1),1);


        return $result;
    }
}
