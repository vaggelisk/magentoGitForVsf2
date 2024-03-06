<?php
/**
 * Description
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Normalize\Adapter;

use Magento\Framework\Model\AbstractModel;
use Netsteps\Marketplace\Api\Data\FeedInterface;
use Netsteps\Seller\Model\Config\FeedTypeOptionsSource;

/**
 * Class Description
 * @package Netsteps\Marketplace\Model\Feed\Normalize\Adapter
 */
class Description extends AbstractAdapter
{
    /**
     * Feed available types to modify description
     * @var array
     */
    private array $_modifiedFeedTypes = [
        FeedTypeOptionsSource::TYPE_LOCALE,
        FeedTypeOptionsSource::TYPE_MASTER
    ];

    /**
     * @inheritDoc
     */
    public function execute(FeedInterface $feed): void
    {
        /** @var $feed AbstractModel */
        if (!$this->canProceed($feed)) {
            return;
        }

        $fileType = ucfirst($feed->getFileType());
        $function = "modify{$fileType}";

        if (!method_exists($this, $function)) {
            return;
        }

        $this->{$function}($feed);
    }

    /**
     * Check if you can proceed for modification
     * @param FeedInterface $feed
     * @return bool
     */
    private function canProceed(FeedInterface $feed): bool
    {
        return $feed->isObjectNew() &&
            in_array($feed->getFileType(), ['xml', 'csv']) &&
            in_array($feed->getFeedType(), $this->_modifiedFeedTypes);
    }

    /**
     * Modify XML
     * @param FeedInterface $feed
     * @return void
     */
    private function modifyXml(FeedInterface $feed): void
    {
        try {
            $feedData = $feed->getFeedData();
            $feedData = mb_ereg_replace('&nbsp;|\'|&', '', $feedData);

            $domDocument = new \DOMDocument();
            $domDocument->loadXML($feedData);
            $descriptions = $domDocument->getElementsByTagName('description');
            $names = $domDocument->getElementsByTagName('name');

            foreach ($descriptions as $description) {
                $text = $description->firstChild->nodeValue;

                if ($text) {
                    $description->firstChild->nodeValue = str_replace('"', '', preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si",'<$1$2>', $text));
                }
            }

            foreach ($names as $name) {
                $name->firstChild->nodeValue = str_replace('"', '', strip_tags($name->firstChild->nodeValue));
            }

            $feedData = $domDocument->saveXML();
            $feed->setFeedData($feedData);
        } catch (\Throwable $throwable) {
            $this->_logger->error(
                __(
                    'Error to modify description for seller %1 in %2 feed. Reason: %3',
                    [$feed->getSellerId(), $feed->getFeedType(), $throwable->getMessage()]
                )
            );
        }
    }

    /**
     * Modify csv
     * @param FeedInterface $feed
     * @return void
     */
    private function modifyCsv(FeedInterface $feed): void
    {
        //TODO implement modifyCsv() method
    }
}
