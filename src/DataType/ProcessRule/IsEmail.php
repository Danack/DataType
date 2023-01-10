<?php

declare(strict_types=1);


namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that a string looks like a valid email address. Does not validate
 * that the domain name is live.
 */
class IsEmail implements ProcessRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        if (strpos($value, "@") === false) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::ERROR_EMAIL_NO_AT_CHARACTER
            );
        }

        $valid = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($valid === false) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::ERROR_EMAIL_INVALID
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setFormat('email');
    }
}
