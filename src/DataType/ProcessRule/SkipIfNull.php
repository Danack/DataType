<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Ends processing if a value is null. This is useful to 'short-circuit' nullable
 * values, e.g. to avoid checking they are between a minimum and maximum value.
 */
class SkipIfNull implements ProcessRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if ($value === null) {
            return ValidationResult::finalValueResult(null);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // If we are allowing null, then parameter must be nullable
        // right?
        // $paramDescription->setNullAllowed();
    }
}
