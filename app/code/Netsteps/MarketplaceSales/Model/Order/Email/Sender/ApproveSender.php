<?php
/**
 * ApproveSender
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email\Sender;

use Magento\Framework\DataObject;

/**
 * Class ApproveSender
 * @package Netsteps\MarketplaceSales\Model\Order\Email\Sender
 */
class ApproveSender extends AbstractSender
{
    protected string $_eventPrefix = 'marketplace_email_order_approve';

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function getTemplateVariables(\Magento\Sales\Api\Data\OrderInterface $order): array
    {
        $transportData = [
            'order' => $order,
            'order_id' => $order->getId(),
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'created_at_formatted' => $order->getCreatedAtFormatted(2),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ],
            'seller_data' => $this->getSellerData($order),
            'delivery' => $this->getDeliveryData($order)
        ];

        $transportObject = new DataObject($transportData);

        /**
         * Dispatch event before set variables
         */
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_vars_before',
            ['order' => $order, 'sender' => $this, 'transport_object' => $transportObject]
        );

        return $transportObject->getData();
    }
}
