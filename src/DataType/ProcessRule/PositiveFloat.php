<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks an input is zero or above and a sane float for a web application. i.e. less than a trillion.
 */
class PositiveFloat implements ProcessRule
{
    use CheckFloat;

    public const MAX_SANE_VALUE = 1_024 * 1_024 * 1_024 * 1_024;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkFloat($value);

        if ($value < 0.0) {
            $message = sprintf(
                Messages::FLOAT_TOO_SMALL,
                0.0
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        $maxValue = (float) self::MAX_SANE_VALUE;
        if ($value > $maxValue) {
            $message = sprintf(
                Messages::FLOAT_TOO_LARGE,
                $maxValue
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
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setMinimum(0.0);
        $paramDescription->setExclusiveMinimum(false);
        $paramDescription->setMaximum((float) self::MAX_SANE_VALUE);
        $paramDescription->setExclusiveMaximum(false);
    }
}
