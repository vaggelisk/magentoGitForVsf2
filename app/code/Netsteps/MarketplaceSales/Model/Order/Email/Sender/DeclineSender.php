<?php
/**
 * DeclineSender
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email\Sender;

use Magento\Framework\DataObject;
use Netsteps\MarketplaceSales\Model\Order\Email\IdentityInterface as Identity;
use Magento\Sales\Api\CreditmemoRepositoryInterface as CreditMemoRepository;
use Magento\Framework\Api\SearchCriteriaBuilder as CriteriaBuilder;

/**
 * Class DeclineSender
 * @package Netsteps\MarketplaceSales\Model\Order\Email\Sender
 */
class DeclineSender extends AbstractSender
{
    protected string $_eventPrefix = 'marketplace_email_order_decline';

    /**
     * @var CreditMemoRepository
     */
    private CreditMemoRepository $_creditMemoRepository;

    /**
     * @var CriteriaBuilder
     */
    private CriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param Identity $identity
     * @param CreditMemoRepository $creditMemoRepository
     * @param CriteriaBuilder $criteriaBuilder
     */
    public function __construct(
        Context $context,
        Identity $identity,
        CreditMemoRepository $creditMemoRepository,
        CriteriaBuilder $criteriaBuilder
    )
    {
        parent::__construct($context, $identity);
        $this->_creditMemoRepository = $creditMemoRepository;
        $this->_searchCriteriaBuilder = $criteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplateVariables(\Magento\Sales\Api\Data\OrderInterface $order): array
    {
        $creditMemo = $this->getLastCreditMemo($order->getEntityId());

        $transportData = [
            'order' => $order,
            'order_id' => $order->getId(),
            'creditmemo' => $creditMemo,
            'creditmemo_id' => $creditMemo ? $creditMemo->getId() : '',
            'comment' => $creditMemo && $creditMemo->getCustomerNoteNotify() ? $creditMemo->getCustomerNote() : '',
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
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

    /**
     * Get last credit memo created
     * @param int $orderId
     * @return \Magento\Sales\Api\Data\CreditmemoInterface|null
     */
    private function getLastCreditMemo(int $orderId): ?\Magento\Sales\Api\Data\CreditmemoInterface {
        $criteria = $this->_searchCriteriaBuilder->addFilter('order_id', $orderId)->create();
        $items = $this->_creditMemoRepository->getList($criteria)->getItems();
        return count($items) === 0 ? null : end($items);
    }
}
