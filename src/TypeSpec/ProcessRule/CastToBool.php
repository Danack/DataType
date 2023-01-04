<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Takes input data and converts it to a bool value, or
 * generates appropriate validationProblems.
 *
 * bool(true) - true
 * bool(false) - false
 * string(true) - true
 * string(1) - true
 * any other string - false
 * int(0) - false
 * any other non-zero int - true
 * any other input - error
 *
 */
class CastToBool implements ProcessPropertyRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if (is_bool($value) === true) {
            return ValidationResult::valueResult($value);
        }

        if (is_string($value) === true) {
            if ($value === 'true') {
                return ValidationResult::valueResult(true);
            }
            else if ($value === 'false') {
                return ValidationResult::valueResult(false);
            }

            $message = sprintf(
                Messages::ERROR_BOOL_BAD_STRING,
                gettype($value)
            );
            return ValidationResult::errorResult($inputStorage, $message);
        }

        $message = sprintf(
            Messages::UNSUPPORTED_TYPE,
            gettype($value)
        );

        return ValidationResult::errorResult($inputStorage, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::FORMAT_BOOLEAN);
    }
}
