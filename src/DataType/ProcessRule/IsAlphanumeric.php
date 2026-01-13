<?php

declare(strict_types=1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that a string contains only letters and numbers (a-z, A-Z, 0-9).
 */
class IsAlphanumeric implements ProcessRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkString($value);

        if (!ctype_alnum($value)) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::ERROR_NOT_ALPHANUMERIC
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setPattern('^[a-zA-Z0-9]+$');
    }
}
