<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Takes user input and converts it to an int value, or
 * generates appropriate validationProblems.
 */
class CastToInt implements ProcessRule
{
    const MAX_SANE_VALUE = 999_999_999_999_999;

    /**
     * Convert a generic input value to an integer
     *
     * @param mixed $value
     * @param ProcessedValues $processedValues
     * @param DataStorage $inputStorage
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if (is_string($value) === false && is_int($value) === false) {
            $message = sprintf(
                Messages::INT_REQUIRED_UNSUPPORTED_TYPE,
                gettype($value)
            );
            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        if (is_string($value) === true) {
            // check string length is not zero length.
            if (strlen($value) === 0) {
                return ValidationResult::errorResult(
                    $inputStorage,
                    Messages::INT_REQUIRED_FOUND_EMPTY_STRING
                );
            }

            //Check only digits.
            $match = preg_match(
                "~        #delimiter
                    ^           # start of input
                    -?          # minus, optional
                    [0-9]+      # at least one digit
                    $           # end of input
                ~xD",
                $value
            );

            if ($match !== 1) {
                return ValidationResult::errorResult(
                    $inputStorage,
                    Messages::INT_REQUIRED_FOUND_NON_DIGITS2
                );
            }
        }

        $maxSaneLength = strlen((string)(self::MAX_SANE_VALUE));
        if (strlen((string)$value) > $maxSaneLength) {
            $message = sprintf(
                Messages::INTEGER_TOO_LONG,
                $maxSaneLength
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult(intval($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::FORMAT_INTEGER);
    }
}
