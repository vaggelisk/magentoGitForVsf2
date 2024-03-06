<?php
/**
 * AbstractCsvValidator
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Validation\Csv;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationException;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface;
use Netsteps\Marketplace\Exception\FileValidationException;
use Netsteps\Marketplace\Model\File\Processor\Csv as CsvProcessor;
use Netsteps\Marketplace\Model\Data\Validator;

/**
 * Class AbstractCsvValidator
 * @package Netsteps\Marketplace\Model\Feed\Validation\Csv
 */
class AbstractCsvValidator implements ValidatorInterface
{
    /**
     * Set of validation rules for this validator
     * @var array
     */
    private array $validationRules;

    /**
     * Invalid csv rows
     * @var array
     */
    private array $invalidRows = [];

    /**
     * @var array
     */
    private array $validated = [];

    /**
     * @var CsvProcessor
     */
    protected CsvProcessor $_csvProcessor;

    /**
     * @param CsvProcessor $csvProcessor
     * @param array $validationRules
     */
    public function __construct(CsvProcessor $csvProcessor,array $validationRules = [])
    {
        $this->_csvProcessor = $csvProcessor;
        $this->validationRules = $validationRules;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function validateSchema(
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
    ): array
    {
        if (empty($this->validationRules)){
            return [];
        }

        if(in_array($feed->getFeedId(), $this->validated)){
            return $this->validated[$feed->getFeedId()];
        }

        if ($feed->getFileType() !== 'csv') {
            $feed->setStatus(FeedMetadataInterface::STATUS_INVALID);
            throw new LocalizedException(
                __('Invalid file type to validate in %1. Expected csv but %2 given',
                    [get_class($this), $feed->getFileType()]
                )
            );
        }

        $decodedData = $processor->processDataAsArray($feed->getFeedData());
        $errors = [];

        $keys = $decodedData[CsvProcessor::KEYS];
        $uniqueKeys = array_unique($keys);

        if (count($keys) !== count($uniqueKeys)){
            $feed->setStatus(FeedMetadataInterface::STATUS_INVALID);
            throw new ValidationException(
                __('Feed has duplicate columns for feed %1', $feed->getFeedId())
            );
        }

        $ruleKeys = array_keys($this->validationRules);

        $missingKeys = array_diff($ruleKeys, $keys);
        if (count($missingKeys)) {
            $feed->setStatus(FeedMetadataInterface::STATUS_INVALID);
            throw new FileValidationException(
                __(
                    'CSV file for feed %1 missing fields (%2)',
                    [$feed->getFeedId(), implode(', ', $missingKeys)]
                )
            );
        }

        $invalidKeys = array_diff($keys, $ruleKeys);
        if (count($invalidKeys)) {
            $feed->setStatus(FeedMetadataInterface::STATUS_INVALID);
            throw new FileValidationException(
                __(
                    'CSV file for feed %1 has invalid fields (%2)',
                    [$feed->getFeedId(), implode(', ', $invalidKeys)]
                )
            );
        }

        $iterator = 2;
        foreach ($decodedData[CsvProcessor::DATA] as $dataValues) {
            if (count($keys) !== count($dataValues)){
                $errors[] = __(
                    'Error on csv in line %1. %2',
                    [$iterator, 'Mismatched field count']
                )->render();
                continue;
            }

            $data = array_combine($keys, $dataValues);
            $validator = new Validator($data, $this->validationRules);

            if (!$validator->isValid()){
                $this->invalidRows[$feed->getFeedId()][] = $iterator;
                foreach ($validator->getErrors() as $error){
                    $errors[] = __('Error on csv in line %1. %2', [$iterator, $error])->render();
                }
            }

            $validator = null;
            $iterator++;
        }

        $this->validated[$feed->getFeedId()] = $errors;
        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function validateRow(array $row): array
    {
        $validator = new Validator($row, $this->validationRules);
        return !$validator->isValid() ? $validator->getErrors() : [];
    }

    /**
     * @inheritDoc
     */
    public function getInvalidRows(\Netsteps\Marketplace\Api\Data\FeedInterface $feed): array
    {
        return $this->invalidRows[$feed->getFeedId()] ?? [];
    }
}
