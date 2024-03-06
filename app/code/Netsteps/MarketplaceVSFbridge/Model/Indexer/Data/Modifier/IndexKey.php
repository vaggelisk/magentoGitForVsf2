<?php
/**
 * IndexKey
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceVSFbridge
 */

namespace Netsteps\MarketplaceVSFbridge\Model\Indexer\Data\Modifier;

use Netsteps\Marketplace\Exception\InvalidValueException;
use Netsteps\MarketplaceVSFbridge\Model\Indexer\Data\ModifierInterface;
use Netsteps\Logger\Model\Logger as Logger;

/**
 * Class IndexKey
 * @package Netsteps\MarketplaceVSFbridge\Model\Indexer\Data\Modifier
 */
class IndexKey implements ModifierInterface
{
    /**
     * @var Logger
     */
    private Logger $_logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @inheritDoc
     * @throws InvalidValueException
     */
    public function modify(array &$data): void
    {
        if (!isset($data['product_id']) || !isset($data['seller_id'])){
            $this->_logger->critical(
                __('Invalid data to index: %1', json_encode($data, JSON_PRETTY_PRINT))
            );

            throw new InvalidValueException(
                __('Invalid data to index. Missing product_id or seller_id')
            );
        }

        $data['id'] = $data['product_id'] . '-' . $data['seller_id'];
        $data['is_in_stock'] = (bool)(int)$data['is_in_stock'];
    }
}
