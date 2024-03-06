<?php
/**
 * AbstractProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\File\Processor;

use Magento\Framework\DataObject;
use Magento\Framework\Validation\ValidationException;
use Netsteps\Marketplace\Model\File\ProcessorInterface;
use Netsteps\Marketplace\Model\Feed\ItemInterfaceFactory as ItemFactory;
use Netsteps\Marketplace\Model\Feed\ItemInterface;
use Magento\Framework\Stdlib\ArrayManager as ArrayManager;
use Netsteps\Marketplace\Traits\ItemVariationTrait;

/**
 * Class AbstractProcessor
 * @package Netsteps\Marketplace\Model\File\Processor
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    use ItemVariationTrait;

    /**
     * @var ArrayManager
     */
    protected ArrayManager $_arrayManager;

    /**
     * @var ItemFactory
     */
    private ItemFactory $_itemFactory;

    /**
     * @param \Netsteps\Marketplace\Model\Feed\ItemInterfaceFactory $itemFactory
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        \Netsteps\Marketplace\Model\Feed\ItemInterfaceFactory $itemFactory,
        ArrayManager $arrayManager
    )
    {
        $this->_itemFactory = $itemFactory;
        $this->_arrayManager = $arrayManager;
    }

    /**
     * Create an item
     * @param array $data
     * @return ItemInterface
     */
    protected function createItem(array $data = []): ItemInterface {
        /** @var  $item ItemInterface|DataObject */
        $item = $this->_itemFactory->create();

        if (!empty($data)){
            $item->setData($data);
        }

        return $item;
    }
}
