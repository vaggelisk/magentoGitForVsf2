<?php
/**
 * ResultInterface
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Processor;

/**
 * Interface ResultInterface
 * @package Netsteps\Marketplace\Model\Processor
 */
interface ResultInterface
{
    const ERRORS = 'errors';
    const PROCESSED_DATA = 'processed_data';
    const ADDITIONAL_DATA = 'additional_data';
    const MESSAGES = 'messages';

    /**
     * Get a set of errors
     * @return string[]
     */
    public function getErrors(): array;

    /**
     * Get any data that may occur from process
     * @return array
     */
    public function getProcessedData(): array;

    /**
     * Get any additional data that may occur from process
     * @return array
     */
    public function getAdditionalData(): array;

    /**
     * Get any messages that may occur from process
     * @return array
     */
    public function getMessages(): array;

    /**
     * Get a set of errors
     * @param array $errors
     * @return ResultInterface
     */
    public function setErrors(array $errors): \Netsteps\Marketplace\Model\Processor\ResultInterface;

    /**
     * Get any data that may occur from process
     * @param array $data
     * @return ResultInterface
     */
    public function setProcessedData(array $data): \Netsteps\Marketplace\Model\Processor\ResultInterface;

    /**
     * Get any additional data that may occur from process
     * @param array $data
     * @return ResultInterface
     */
    public function setAdditionalData(array $data): \Netsteps\Marketplace\Model\Processor\ResultInterface;

    /**
     * Get any messages that may occur from process
     * @param array $messages
     * @return ResultInterface
     */
    public function setMessages(array $messages): \Netsteps\Marketplace\Model\Processor\ResultInterface;
}
