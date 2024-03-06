<?php
/**
 * Export
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSellerCommunication
 */

namespace Netsteps\MarketplaceSales\Controller\Adminhtml\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Export
 * @package Netsteps\MarketplaceSales\Controller\Adminhtml\Order
 */
class Export extends AbstractOrderAction
{
    /**
     * Order's data as array
     * @var array
     */
    private array $_orderData = [];

    /**
     * File name placeholder
     * @var string
     */
    private string $_fileName = 'Order-%s.json';

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _execute(OrderInterface $order): void
    {
        try {
            $orderData = $this->_orderDataManagement->createOrderData($order);
            $this->_orderData = $this->_outputProcessor->convertValue(
                $orderData,
                \Netsteps\MarketplaceSales\Api\Data\OrderInterface::class
            );
            $this->_fileName = sprintf($this->_fileName, $order->getIncrementId());

        } catch (\Exception $e) {
            $this->_logger->critical(
                __('Error on export order JSON. Reason: %1', $e->getMessage())
            );
            throw new LocalizedException(
                __('An error occurred during export process.')
            );
        }
    }

    /**
     * Get file response
     * @return \Magento\Framework\App\ResponseInterface|null
     * @throws \Exception
     */
    protected function getResultOrResponse(): ?\Magento\Framework\App\ResponseInterface
    {
        if (empty($this->_orderData)){
            return null;
        }

        return $this->_fileFactory->create(
            $this->_fileName,
            [
                'type' => 'string',
                'value' => json_encode($this->_orderData, JSON_PRETTY_PRINT),
                'rm' => true
            ]
        );
    }
}
