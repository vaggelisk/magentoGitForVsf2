<?php
/**
 * DataValidationTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Traits;

/**
 * Trait DataValidationTrait
 * @package Netsteps\Marketplace\Traits
 */
trait DataValidationTrait
{
    /**
     * Validation method map
     * @var array|string[]
     */
    protected array $validationMethodMap = [
        'required' => 'isRequired',
        'minLength' => 'minLength',
        'maxLength' => 'maxLength',
        'allowed' => 'hasAllowedValue',
        'positiveNumber' => 'isPositive',
        'min' => 'minValue',
        'max'=> 'maxValue'
    ];

    /**
     * Validation method map
     * @var array|string[]
     */
    protected array $validationMessageMap = [
        'required' => 'Field %1 is required',
        'minLength' => 'Field %1 must have min length of %2',
        'maxLength' => 'Field %1 must have max length of %2',
        'allowed' => 'Field %1 should be one of "%2"',
        'positiveNumber' => 'Field %1 should be a positive number',
        'min' => 'Field %1 must have a minimum value of %2',
        'max'=> 'Field %1 must have a maximum value of %2'
    ];

    /**
     * Validate
     * @param string $validationKey
     * @param string|null $value
     * @param string|null $validationValue
     * @return bool
     */
    protected function validate(string $validationKey, ?string $value, ?string $validationValue): bool {
        if (is_null($value) || $value === ''){
            return $validationKey !== 'required';
        }

        $fn = $this->validationMethodMap[$validationKey] ?? false;

        if (!$fn){
            return true;
        }

        if (is_null($validationValue)){
            return $this->{$fn}($value);
        }  else {
            return $this->{$fn}($value, $validationValue);
        }
    }

    /**
     * Check if value is required
     * @param string $value
     * @return bool
     */
    protected function isRequired(string $value): bool {
        return trim($value) !== '';
    }

    /**
     * Check value min length
     * @param string $value
     * @param string $length
     * @return bool
     */
    protected function minLength(string $value, string $length): bool {
        return mb_strlen($value) >= (int)$length;
    }

    /**
     * Check value max length
     * @param string $value
     * @param string $length
     * @return bool
     */
    protected function maxLength(string $value, string $length): bool {
        return  mb_strlen($value) <= (int)$length;
    }

    /**
     * Check if is decimal
     * @param string $value
     * @return bool
     */
    protected function isDecimal(string $value): bool {
        return (bool)preg_match('/^\d+\.\d+$/', $value);
    }

    /**
     * Check if is integer
     * @param string $value
     * @return bool
     */
    protected function isInteger(string $value): bool {
        return (bool)preg_match('/^\d+$/', $value);
    }

    /**
     * Check if value is in allowed value
     * @param string $value
     * @param string $acceptedValues
     * @return bool
     */
    protected function hasAllowedValue(string $value, string $acceptedValues): bool {
        return in_array($value, explode(',', $acceptedValues));
    }

    /**
     * Check if is positive number
     * @param string $value
     * @return bool
     */
    protected function isPositive(string $value): bool {
        return is_numeric($value) && ((float)$value) > 0;
    }

    /**
     * Check value min value
     * @param string $value
     * @param string $minValue
     * @return bool
     */
    protected function minValue(string $value, string $minValue): bool {
        return is_numeric($value) && ((float)$value) >= ((float)$minValue);
    }

    /**
     * Check value max length
     * @param string $value
     * @param string $maxValue
     * @return bool
     */
    protected function maxValue(string $value, string $maxValue): bool {
        return is_numeric($value) && ((float)$value) <= ((float)$maxValue);
    }

    /**
     * Get error message
     * @param string $validationKey
     * @return string|null
     */
    protected function getErrorMessage(string $validationKey): ?string {
        return $this->validationMessageMap[$validationKey] ?? null;
    }
}
