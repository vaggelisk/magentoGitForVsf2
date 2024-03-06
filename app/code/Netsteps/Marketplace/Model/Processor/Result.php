<?php
/**
 * Result
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Processor;

use Magento\Framework\DataObject;

/**
 * Class Result
 * @package Netsteps\Marketplace\Model\Processor
 */
class Result extends DataObject implements ResultInterface
{
    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {
        return $this->getValidArrayResult(self::ERRORS);
    }

    /**
     * @inheritDoc
     */
    public function getProcessedData(): array
    {
        return $this->getValidArrayResult(self::PROCESSED_DATA);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData(): array
    {
        return $this->getValidArrayResult(self::ADDITIONAL_DATA);
    }

    /**
     * @inheritDoc
     */
    public function getMessages(): array
    {
       return $this->getValidArrayResult(self::MESSAGES);
    }

    /**
     * Get data as valid array
     * @param string $key
     * @return array
     */
    private function getValidArrayResult(string $key): array {
        $data = $this->_getData($key);
        return is_array($data) ? $data : [];
    }

    /**
     * @inheritDoc
     */
    public function setErrors(array $errors): \Netsteps\Marketplace\Model\Processor\ResultInterface
    {
        return $this->setData(self::ERRORS, $errors);
    }

    /**
     * @inheritDoc
     */
    public function setProcessedData(array $data): \Netsteps\Marketplace\Model\Processor\ResultInterface
    {
        return $this->setData(self::PROCESSED_DATA, $data);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalData(array $data): \Netsteps\Marketplace\Model\Processor\ResultInterface
    {
        return $this->setData(self::ADDITIONAL_DATA, $data);
    }

    /**
     * @inheritDoc
     */
    public function setMessages(array $messages): \Netsteps\Marketplace\Model\Processor\ResultInterface
    {
        return $this->setData(self::MESSAGES, $messages);
    }
}
