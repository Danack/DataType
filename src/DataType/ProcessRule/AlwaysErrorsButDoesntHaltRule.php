<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Rule that always fails validation but does not stop processing.
 *
 * This is currently used for testing mostly.
 */
class AlwaysErrorsButDoesntHaltRule implements ProcessRule
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        return ValidationResult::errorButContinueResult(
            $value,
            $inputStorage,
            $this->message
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing.
    }
}
