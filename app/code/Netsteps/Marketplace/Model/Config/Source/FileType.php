<?php
/**
 * FileType
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Kostas Tsiapalis
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Config\Source;

/**
 * Class FileType
 * @package Netsteps\Marketplace\Model\Config\Source
 */
class FileType extends AbstractMetadataSource
{
    private ?array $_options = null;

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if (is_null($this->_options)){
            $this->_options = $this->transformSingleArrayToOptions(
                $this->_metadata->getAcceptedFileTypes()
            );
        }

        return $this->_options;
    }
}
