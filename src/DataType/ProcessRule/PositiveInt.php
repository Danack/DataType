<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;
use function DataType\check_only_digits;

/**
 * Class PositiveIntValidator
 *
 * Checks an input is above zero and a sane int.
 */
class PositiveInt implements ProcessRule
{
    const MAX_SANE_VALUE = 1_024 * 1_024 * 1_024 * 1_024;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        /**  @psalm-suppress MixedArgument */
        $errorMessage = check_only_digits($value);
        if ($errorMessage !== null) {
            return ValidationResult::errorResult($inputStorage, $errorMessage);
        }

        $value = intval($value);
        $maxValue = self::MAX_SANE_VALUE;
        if ($value > $maxValue) {
            $message = sprintf(
                Messages::INT_OVER_LIMIT,
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
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setMinimum(0);
        $paramDescription->setExclusiveMinimum(false);
    }
}
