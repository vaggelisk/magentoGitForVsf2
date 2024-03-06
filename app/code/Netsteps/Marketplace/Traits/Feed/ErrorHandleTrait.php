<?php
/**
 * ErrorHandleTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Traits\Feed;

/**
 * Trait ErrorHandleTrait
 * @package Netsteps\Marketplace\Traits\Feed
 */
trait ErrorHandleTrait
{
    /**
     * Handle exception errors info
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param \Exception $e
     * @return void
     */
    protected function handleExceptionErrors(\Netsteps\Marketplace\Api\Data\FeedInterface $feed, \Exception $e): void {
        $this->handle($feed, 'exception', $e);
    }

    /**
     * Handle validation errors
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param array $errors
     * @return void
     */
    protected function handleValidationErrors(\Netsteps\Marketplace\Api\Data\FeedInterface $feed, array $errors) {
        $this->handle($feed, 'validation', $errors);
    }

    /**
     * Handle process errors
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param array $errors
     * @return void
     */
    protected function handleProcessErrors(\Netsteps\Marketplace\Api\Data\FeedInterface $feed, array $errors) {
        $this->handle($feed, 'process', $errors);
    }

    /**
     * Handle schema errors
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param array $errors
     * @return void
     */
    protected function handleSchemaErrors(\Netsteps\Marketplace\Api\Data\FeedInterface $feed, array $errors) {
        $this->handle($feed, 'schema', $errors);
    }

    /**
     * Handle errors for feed
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @param string $type
     * @param mixed $error
     * @return void
     */
    private function handle(\Netsteps\Marketplace\Api\Data\FeedInterface $feed, string $type, mixed $error): void {
        $errors = $this->normalizeError($error);

        if (empty($errors)) {
            return;
        }

        $existingInfo = $feed->getAdditionalInfo();
        $data = @json_decode($existingInfo, true);

        if (!is_array($data)){
            $data = [];
        }

        if (isset($data[$type])) {
            $data[$type] = array_merge($data[$type], $errors);
        } else {
            $data[$type] = $errors;
        }

        $feed->setAdditionalInfo(json_encode($data));
    }

    /**
     * Normalize error
     * @param mixed $error
     * @return array
     */
    private function normalizeError(mixed $error): array {
        if ($error instanceof \Exception) {
            return [$error->getMessage()];
        } else {
            $errors = [];
            switch (gettype($error)){
                case 'string':
                    $errors[] = $error;
                    break;
                case 'array':
                    $errors = $error;
                    break;
            }

            return $errors;
        }
    }
}
