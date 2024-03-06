<?php
/**
 * InvoiceProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Sales\Model\Service\InvoiceService as InvoiceService;
use Magento\Sales\Api\InvoiceRepositoryInterface as InvoiceRepository;
use Magento\Framework\DB\Transaction as DbTransaction;

/**
 * Class InvoiceProcessor
 * @package Netsteps\MarketplaceSales\Model\Order
 */
class InvoiceProcessor implements InvoiceProcessorInterface
{
    /**
     * @var OrderRepository
     */
    private OrderRepository $_orderRepository;

    /**
     * @var InvoiceService
     */
    private InvoiceService $_invoiceService;

    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $_invoiceRepository;

    /**
     * @var DbTransaction
     */
    private DbTransaction $_transaction;

    /**
     * @param OrderRepository $orderRepository
     * @param InvoiceService $invoiceService
     * @param InvoiceRepository $invoiceRepository
     * @param DbTransaction $transaction
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceService  $invoiceService,
        InvoiceRepository $invoiceRepository,
        DbTransaction   $transaction
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_transaction = $transaction;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @throws \Exception
     * @var \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     */
    public function invoice(\Magento\Sales\Api\Data\OrderInterface $order): int
    {
        if (!$order->canInvoice()) {
            throw new LocalizedException(
                __('Order %1 can not be invoiced.', $order->getIncrementId())
            );
        }

        $invoice = $this->_invoiceService->prepareInvoice($order);
        $invoice->register();
        $this->_invoiceRepository->save($invoice);
        $order->setIsCustomerNotified(true);

        $order->addCommentToStatusHistory('Order invoiced automatically from split process');
        $this->_orderRepository->save($order);

        return $invoice->getId();
    }

    /**
     * @inheritDoc
     */
    public function invoiceByOrderId(int $orderId): int
    {
        $order = $this->_orderRepository->get($orderId);
        return $this->invoice($order);
    }
}
