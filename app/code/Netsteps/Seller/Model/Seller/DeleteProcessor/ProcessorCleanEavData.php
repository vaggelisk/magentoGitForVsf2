<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\DeleteProcessor;

use Netsteps\Seller\Model\Product\CleanDistributorData;

class ProcessorCleanEavData implements DeletePoolInterface
{
    private CleanDistributorData $cleanDistributorData;

    public function __construct(CleanDistributorData $cleanDistributorData)
    {
        $this->cleanDistributorData = $cleanDistributorData;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $data = []): void
    {
        if(isset($data['seller']) && $data['seller'] instanceof \Netsteps\Seller\Api\Data\SellerInterface){
            $this->cleanDistributorData->deleteAttributeData((int)$data['seller']->getEntityId());
        }
    }
}
