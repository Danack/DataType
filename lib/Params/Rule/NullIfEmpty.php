<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

/**
 * Convert the value to null if the string is empty, and provides
 * a final result
 */
class NullIfEmpty implements Rule
{
    public function __invoke(string $name, $value): ValidationResult
    {
        if ($value === null) {
            return ValidationResult::finalValueResult(null);
        }

        $temp_value = (string)$value;
        $temp_value = trim($temp_value);

        if (strlen($temp_value) === 0) {
            return ValidationResult::finalValueResult(null);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // If we are allowing null, then parameter must be nullable
        // right?
        $paramDescription->setNullAllowed();
    }
}
