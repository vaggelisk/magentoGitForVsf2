<?php
/**
 * PendingApproval
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Email\Sender;

use Magento\Framework\Exception\LocalizedException;
use Netsteps\MarketplaceSales\Model\Order\Email\IdentityInterface as Identity;
use Netsteps\Seller\Api\Data\SellerInterface;
use Magento\Framework\View\LayoutInterface as Layout;

/**
 * Class PendingApproval
 * @package Netsteps\MarketplaceSales\Model\Order\Email\Sender
 */
class PendingApproval extends AbstractSender
{
    /**
     * @var array
     */
    private array $_orders = [];

    /**
     * @var SellerInterface|null
     */
    private ?SellerInterface $_seller = null;

    /**
     * @var Layout
     */
    private Layout $_layout;

    /**
     * @param Context $context
     * @param Identity $identity
     * @param Layout $layout
     */
    public function __construct(
        Context $context,
        Identity $identity,
        Layout $layout
    )
    {
        $this->_layout = $layout;
        parent::__construct($context, $identity);
    }

    /**
     * Set expired information
     * @param SellerInterface $seller
     * @param \Magento\Sales\Api\Data\OrderInterface[] $orders
     * @return $this
     */
    public function setExpiredInformation(SellerInterface $seller, array $orders): self {
        $this->_seller = $seller;
        $this->_orders = $orders;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplateVariables(\Magento\Sales\Api\Data\OrderInterface $order): array
    {
        return [
            'pending_orders_html' => $this->getPendingOrdersHtml(),
            'seller' => $this->_seller,
            'seller_data' => [
                'name' => $this->_seller->getName(),
                'email' => $this->_seller->getEmail()
            ]
        ];
    }

    /**
     * Get recipient information
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    protected function getRecipient(\Magento\Sales\Api\Data\OrderInterface $order): array
    {
        return ['name' => $this->_seller->getName(), 'email' => $this->_seller->getEmail()];
    }

    /**
     * Override send to validate data
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     * @throws LocalizedException
     */
    public function send(\Magento\Sales\Api\Data\OrderInterface $order): void
    {
        if (!$this->_seller || empty($this->_orders)){
            return;
        }

        if (!$this->_seller->getEmail() || $this->_seller->getEmail() === ''){
            throw new LocalizedException(
                __('Seller %1 does not have email.', $this->_seller->getName())
            );
        }

        parent::send($order);
    }

    /**
     * Get pending orders' table html
     * @return string
     */
    private function getPendingOrdersHtml(): string {
        try {
            /** @var  $block \Netsteps\MarketplaceSales\Block\Email\Order\PendingApproval */
            $block = $this->_layout->createBlock(\Netsteps\MarketplaceSales\Block\Email\Order\PendingApproval::class);
            $block->setOrders($this->_orders);
            return $block->toHtml();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return '';
    }
}
