<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates the input value is equal to or greater than a particular float value.
 *
 * TODO: Refactor so $value is typed (e.g. mixed) or validated before floatval() to satisfy
 * PHPStan without ignoring. This is internal process-rule code; the ignore is acceptable for now.
 */
class MinFloatValue implements ProcessRule
{
    public function __construct(private float $minValue)
    {
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = floatval($value);
        if ($value < $this->minValue) {
            $message = sprintf(
                Messages::FLOAT_TOO_SMALL,
                $this->minValue
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
        $paramDescription->setMinimum($this->minValue);
        $paramDescription->setExclusiveMinimum(false);
    }
}
