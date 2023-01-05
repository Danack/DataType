<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Validates that an input or processed value is not null.
 */
class NotNull implements ProcessPropertyRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if ($value === null) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::NULL_NOT_ALLOWED
            );
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setNullAllowed(false);
    }
}
