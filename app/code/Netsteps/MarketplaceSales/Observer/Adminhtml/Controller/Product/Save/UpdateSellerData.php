<?php
/**
 * UpdateSellerData
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Observer\Adminhtml\Controller\Product\Save;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\MarketplaceSales\Api\ProductManagementInterface as ProductManagement;
use Netsteps\MarketplaceSales\Model\Product\Metadata;
use Magento\Framework\Message\ManagerInterface as MessageManager;

/**
 * Class RepositoryPlugin
 * @package Netsteps\MarketplaceSales\Plugin\Adminhtml\Product
 */
class UpdateSellerData implements ObserverInterface
{
    /**
     * @var ProductManagement
     */
    private ProductManagement $_productManagement;

    /**
     * @var MessageManager
     */
    private MessageManager $_messageManager;

    /**
     * @param ProductManagement $productManagement
     * @param MessageManager $manager
     */
    public function __construct(ProductManagement $productManagement, MessageManager $manager)
    {
        $this->_productManagement = $productManagement;
        $this->_messageManager = $manager;
    }

    /**
     * Update product data for seller (lowest seller id, json data, seller discount percentage)
     * after save product.
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            if (in_array($product->getTypeId(), Metadata::getProductIndexedTypes())){
                try {
                    $this->_productManagement->updateProductsData([$product->getId()]);
                    $this->_messageManager->addSuccessMessage(__('Seller data updated successfully.'));
                } catch (\Exception $e) {
                    $this->_messageManager->addWarningMessage(
                        __('An error occurred on seller data update process.')
                    );
                }
            }
        }
    }
}
