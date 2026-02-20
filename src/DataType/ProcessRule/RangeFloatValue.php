<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that a float value is between a range of float values inclusive.
 *
 * TODO: Refactor so $value is typed (e.g. mixed) or validated before floatval() to satisfy
 * PHPStan without ignoring. This is internal process-rule code; the ignore is acceptable for now.
 */
class RangeFloatValue implements ProcessRule
{
    /**
     *
     * @param float $minValue Value is inclusive
     * @param float $maxValue Value is inclusive
     */
    public function __construct(
        private float $minValue,
        private float $maxValue
    ) {
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

        if ($value > $this->maxValue) {
            $message = sprintf(
                Messages::FLOAT_TOO_LARGE,
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
        $paramDescription->setMinimum($this->minValue);
        $paramDescription->setExclusiveMinimum(false);

        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
