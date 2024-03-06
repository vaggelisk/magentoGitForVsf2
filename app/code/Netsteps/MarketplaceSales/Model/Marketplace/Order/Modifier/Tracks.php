<?php
/**
 * Tracks
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier;

/**
 * Class Tracks
 * @package Netsteps\MarketplaceSales\Model\Marketplace\Order\Modifier
 */
class Tracks extends AbstractModifier
{
    /**
     * @inheritDoc
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Netsteps\MarketplaceSales\Api\Data\OrderInterface $orderData,
        ?\Magento\Sales\Api\Data\OrderInterface $parentOrder = null
    ): void
    {
        /** @var $order \Magento\Sales\Model\Order */
        $tracks = [];
        $shipments = $order->getShipmentsCollection();

        if (!$shipments) {
            return;
        }

        /** @var  $shipment \Magento\Sales\Api\Data\ShipmentInterface */
        foreach ($shipments as $shipment) {
            $shipmentTracks = $shipment->getTracks();

            if (empty($shipmentTracks)){
                continue;
            }

            $tracks = array_merge($tracks, $shipmentTracks);
        }

        foreach ($tracks as $track) {
            $track->unsOrderId();
            $track->unsEntityId();
            $track->unsParentId();
        }

        $orderData->setTracks($tracks);
    }
}
