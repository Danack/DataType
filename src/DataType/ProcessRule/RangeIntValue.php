<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that a int value is between a range of int values inclusive.
 */
class RangeIntValue implements ProcessRule
{
    use CheckInt;
    private int $minValue;

    private int $maxValue;

    /**
     *
     * @param int $minValue Value is inclusive
     * @param int $maxValue Value is inclusive
     */
    public function __construct(
        int $minValue,
        int $maxValue
    ) {
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkInt($value);
        if ($value < $this->minValue) {
            $message = sprintf(
                Messages::INT_TOO_SMALL,
                $this->minValue
            );
            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

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
        $paramDescription->setMinimum($this->minValue);
        $paramDescription->setExclusiveMinimum(false);

        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
