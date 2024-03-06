<?php
/**
 * Validator
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Data;

use Netsteps\Marketplace\Traits\DataValidationTrait;

/**
 * Class Validator
 * @package Netsteps\Marketplace\Model\Data
 */
class Validator
{
    use DataValidationTrait;

    /**
     * @var array
     */
    private static array $ruleCache = [];

    /**
     * @var array
     */
    private array $_data;

    /**
     * @var array
     */
    private array $_rules;

    /**
     * @var bool|null
     */
    private ?bool $_isValid = null;

    /**
     * @var array
     */
    private array $_errors = [];

    /**
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules) {
        $this->_data = $data;
        $this->_rules = $this->parseRules($rules);
    }

    /**
     * Parsing rules
     * @param array $rules
     * @return array
     */
    private function parseRules(array $rules): array {
        $cacheKey = crc32(json_encode($rules));

        if (isset(self::$ruleCache[$cacheKey])){
            return self::$ruleCache[$cacheKey];
        }

        $rulesParsed = [];

        foreach ($rules as $field => $rulesSerialized) {
            $fieldRules = [];
            $ruleParts = explode('|', $rulesSerialized);

            foreach ($ruleParts as $rulePart) {
                $ruleParts = explode(':', $rulePart);
                $fieldRules[$ruleParts[0]] = @($ruleParts[1]) ?? null;
            }

            $rulesParsed[$field] = $fieldRules;
        }

        self::$ruleCache[$cacheKey] = $rulesParsed;

        return $rulesParsed;
    }

    /**
     * Check if is valid the given data
     * @return bool
     */
    public function isValid(): bool {
        if (is_bool($this->_isValid)){
            return $this->_isValid;
        }

        $errors = [];

        foreach ($this->_data as $dataKey => $dataValue) {
            $rules = $this->_rules[$dataKey] ?? false;

            if (!$rules){
                continue;
            }

            foreach ($rules as $fnKey => $fnValue) {
                if (!$this->validate($fnKey, $dataValue, $fnValue)){
                    $message = $this->getErrorMessage($fnKey) ?? 'Field %1 is invalid';
                    $errors[] = __($message, [$dataKey, $fnValue]);
                }
            }
        }

        $this->_errors = $errors;
        $this->_isValid = count($this->_errors) === 0;
        return $this->_isValid;
    }

    /**
     * Get errors
     * @return string[]
     */
    public function getErrors(): array {
        return $this->_errors;
    }
}
