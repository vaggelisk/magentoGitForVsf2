<?php
/**
 * SourceDeductionProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Plugin;

use Ampersand\DisableStockReservation\Plugin\SourceDeductionProcessor as BaseDeductionProcessor;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Service\OrderService;
use Netsteps\MarketplaceSales\Traits\ConfigurationTrait;
use Netsteps\MarketplaceSales\Model\System\Config\Source\OrderType as OrderType;

/**
 * Class SourceDeductionProcessor
 * @package Netsteps\MarketplaceSales\Plugin
 */
class SourceDeductionProcessor extends BaseDeductionProcessor
{
    use ConfigurationTrait;

    /**
     * Override Ampersand plugin method for split orders to avoid further deduction
     * @param OrderService $subject
     * @param OrderInterface $result
     * @return OrderInterface|void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterPlace(OrderService $subject, OrderInterface $result)
    {
        $orderType = OrderType::getOrderType($result);
        $disabledStockDeductionTypes = $this->getConfig()->getDisabledOrderTypesForStockDeduction();

        if (in_array($orderType, $disabledStockDeductionTypes)){
            return $result;
        }

        return parent::afterPlace($subject, $result);
    }
}
