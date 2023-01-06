<?php

declare(strict_types=1);


namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Class IsEmail
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
