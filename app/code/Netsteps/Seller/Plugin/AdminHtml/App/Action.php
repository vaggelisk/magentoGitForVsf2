<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Plugin\AdminHtml\App;

use Netsteps\Seller\Model\Admin\AttachSeller;

class Action
{

    private AttachSeller $attachSeller;

    /**
     * @param AttachSeller $attachSeller
     */
    public function __construct(AttachSeller $attachSeller)
    {
        $this->attachSeller = $attachSeller;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeExecute(\Magento\Framework\App\ActionInterface $subject)
    {
        $this->attachSeller->execute();
        return [];
    }

}
