<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Config;

use Magento\InventoryApi\Api\SourceRepositoryInterface;

class InventorySourceCodesOptionsSource implements \Magento\Framework\Data\OptionSourceInterface
{

    private SourceRepositoryInterface $sourceRepository;

    public function __construct(SourceRepositoryInterface $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $data[] = ['value' => '', 'label' => ' '];
        $sources = $this->getInvetorySourceCodes();
        foreach ($sources as $source) {
            $data[] = [
                'value' => $source->getSourceCode(),
                'label' => $source->getName()
            ];
        }
        return $data;
    }

    /**
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    private function getInvetorySourceCodes(): array
    {
        return $this->sourceRepository->getList()->getItems();
    }
}
