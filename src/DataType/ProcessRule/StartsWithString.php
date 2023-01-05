<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Check that a string starts with a particular prefix.
 */
class StartsWithString implements ProcessRule
{
    use CheckString;

    private string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        if (strpos($value, $this->prefix) !== 0) {
            $message = sprintf(
                Messages::STRING_REQUIRES_PREFIX,
                $this->prefix
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        // This rule does not modify the value
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO implement
    }
}
