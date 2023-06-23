<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Convert the value to null if the string is empty or blank, and provides
 * a final result.
 */
class NullIfEmptyString implements ProcessRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if ($value === null) {
            return ValidationResult::finalValueResult(null);
        }

        $value = $this->checkString($value);

        $temp_value = $value;
        $temp_value = trim($temp_value);

        if (mb_strlen($temp_value) === 0) {
            return ValidationResult::finalValueResult(null);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // If we are allowing null, then parameter
        // must be nullable, right?
        $paramDescription->setNullAllowed(true);
    }
}
