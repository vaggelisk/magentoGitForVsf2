<?php
/**
 * SellerManagement
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Admin;

use Magento\Framework\App\Request\DataPersistorInterface as DataPersistor;


/**
 * Class SellerManagement
 * @package Netsteps\Seller\Model\Admin
 */
class SellerManagement implements SellerManagementInterface
{
    /**
     * @var DataPersistor
     */
    private DataPersistor $_dataPersistor;

    /**
     * @param DataPersistor $dataPersistor
     */
    public function __construct(DataPersistor $dataPersistor)
    {
        $this->_dataPersistor = $dataPersistor;
    }

    /**
     * @inheritDoc
     */
    public function getLoggedSeller(): ?\Netsteps\Seller\Api\Data\SellerInterface
    {
        $seller = $this->_dataPersistor->get(AttachSeller::REGISTRY_KEY);
        return $seller instanceof \Netsteps\Seller\Api\Data\SellerInterface ? $seller : null;
    }

    /**
     * @inheritDoc
     */
    public function getLoggedSellerId(): ?int {
        $seller = $this->getLoggedSeller();
        return $seller ? (int)$seller->getEntityId() : null;
    }

    /**
     * @inheritDoc
     */
    public function getSellerGroup(): ?string {
        $seller = $this->getLoggedSeller();
        return $seller ? $seller->getGroup() : null;
    }
}
