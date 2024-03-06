<?php
/**
 * SellerManagementInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Admin;

/**
 * Interface SellerManagementInterface
 * @package Netsteps\Seller\Model\Admin
 */
interface SellerManagementInterface
{
    /**
     * Get logged in seller entity
     * @return \Netsteps\Seller\Api\Data\SellerInterface|null
     */
    public function getLoggedSeller(): ?\Netsteps\Seller\Api\Data\SellerInterface;

    /**
     * Get logged in seller id
     * @return int|null
     */
    public function getLoggedSellerId(): ?int;

    /**
     * Get logged in seller group
     * @return string|null
     */
    public function getSellerGroup(): ?string;
}
