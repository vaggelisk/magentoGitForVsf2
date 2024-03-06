<?php
/**
 * SubActionManager
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Action;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Netsteps\Seller\Api\Data\SellerInterface;

/**
 * Class SubActionManager
 * @package Netsteps\Marketplace\Model\Feed\Action
 */
class SubActionManager implements SubActionManagerInterface
{
    /**
     * @var SubActionInterface[]
     */
    private array $actions;

    /**
     * @var SellerInterface
     */
    private SellerInterface $seller;

    /**
     * Registered actions
     * @var array
     */
    private array $registry = [];

    /**
     * @param SellerInterface $seller
     * @param SubActionInterface[] $actions
     */
    public function __construct(
        SellerInterface $seller,
        array $actions = []
    )
    {
        $this->seller = $seller;
        $this->actions = $actions;
    }

    /**
     * @inheritDoc
     */
    public function getSeller(): SellerInterface
    {
        return $this->seller;
    }

    /**
     * @inheritDoc
     */
    public function addActionItem(string $action, DataObject $item): SubActionManagerInterface
    {
        $this->registry[$action][] = $item;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resolve(): void
    {
        foreach ($this->registry as $actionCode => $data) {
            try {
                $action = $this->actions[$actionCode];
                $action->process($data, $this->seller);
                unset($this->registry[$actionCode]);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function resolveByAction(string $action): void
    {
       if (!isset($this->registry[$action])){
           return;
       }

       if (!isset($this->actions[$action])) {
           throw new LocalizedException(
               __('Action %1 is not defined', $action)
           );
       }

       $data = $this->registry[$action];
       $this->actions[$action]->process($data, $this->seller);
    }
}
