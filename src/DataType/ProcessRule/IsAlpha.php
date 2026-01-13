<?php

declare(strict_types=1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that a string contains only letters (a-z, A-Z).
 */
class IsAlpha implements ProcessRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkString($value);

        if (!ctype_alpha($value)) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::ERROR_NOT_ALPHA
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setPattern('^[a-zA-Z]+$');
    }
}
