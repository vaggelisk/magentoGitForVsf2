<?php

namespace Netsteps\Seller\Controller\Adminhtml\Seller;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Controller\ResultFactory;

class Upload extends Action
{
    /**
     * Image uploader
     *
     * @var ImageUploader
     */
    protected ImageUploader $_imageUploader;

    /**
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->_imageUploader = $imageUploader;
    }

    public function execute()
    {
        $fieldId = [];
        $files = $this->getRequest()->getFiles();
        if(isset($files['seller']) && isset($files['seller']['options']) && isset($files['seller']['options']['logo'])) {
            $fieldId = $files['seller']['options']['logo'];
        }

        try {
            $this->_imageUploader->setBaseTmpPath('logo');
            $result = $this->_imageUploader->saveFileToTmpDir($fieldId);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
