<?php
/**
 * ImageProcessor
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Netsteps\Marketplace\Traits\StringModifierTrait;

/**
 * Class ImageProcessor
 * @package Netsteps\Marketplace\Model\Product
 */
class ImageProcessor
{
    use StringModifierTrait;

    /**
     * Directory List
     *
     * @var DirectoryList
     */
    protected DirectoryList $directoryList;

    /**
     * File interface
     *
     * @var File
     */
    protected File $file;

    /**
     * @param DirectoryList $directoryList
     * @param File $file
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
    }

    /**
     * Image executor
     * @param Product $product
     * @param string $imageUrl
     * @param array $imageType
     * @param bool $visible
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Product $product, string $imageUrl, bool $visible = true, array $imageType = []): bool
    {
        $tmpDir = $this->getMediaDirTmpDir();
        $this->file->checkAndCreateFolder($tmpDir);

        /** Replace empty spaces with %20 to handle filenames with spaces */
        $imageUrl = str_replace(' ', '%20', $imageUrl);

        $filename = $this->normalizeFilename($imageUrl);
        $newFileName = $tmpDir . $filename;

        $result = $this->file->read($imageUrl, $newFileName);

        if ($result) {
            $product->addImageToMediaGallery($newFileName, $imageType, true, !$visible);
        }

        return $result;
    }

    /**
     * Media directory name for the temporary file storage
     * pub/media/tmp
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getMediaDirTmpDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    }
}
