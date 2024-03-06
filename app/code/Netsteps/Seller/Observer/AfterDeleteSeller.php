<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Netsteps\Seller\Model\Seller\DeleteProcessor\DeletePoolInterface;

class AfterDeleteSeller implements ObserverInterface
{
    /**
     * @var DeletePoolInterface[]
     */
    private array $processors;

    /**
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    )
    {
        $this->processors = $processors;
    }

    /**
     * Apply model delete operation
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Validator\Exception
     */
    public function execute(Observer $observer)
    {

        /** @var \Netsteps\Seller\Api\Data\SellerInterface $seller */
        $seller = $observer->getEvent()->getSeller();
        if (!$seller->getEntityId() || empty($this->processors)) {
            return;
        }

        foreach($this->processors as $processor){
            $processor->execute(['seller' => $seller]);
        }
    }
}

