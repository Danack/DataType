<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Trims whitespace off the start and end of strings.
 */
class Trim implements ProcessRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        return ValidationResult::valueResult(trim($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Should update description?
    }
}
