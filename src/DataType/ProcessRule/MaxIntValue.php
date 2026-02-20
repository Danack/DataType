<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates the input value is equal to or less than a particular int value.
 *
 * TODO: Refactor so $value is typed (e.g. mixed) or validated before intval() to satisfy
 * PHPStan without ignoring. This is internal process-rule code; the ignore is acceptable for now.
 */
class MaxIntValue implements ProcessRule
{
    private int $maxValue;

    public function __construct(int $maxValue)
    {
        $this->maxValue = $maxValue;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = intval($value);
        if ($value > $this->maxValue) {
            $message = sprintf(
                Messages::INT_TOO_LARGE,
                $this->maxValue
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
