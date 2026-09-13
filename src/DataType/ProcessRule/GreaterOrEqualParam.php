<?php

declare(strict_types=1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that this parameter is greater than or equal to another previously processed parameter.
 */
class GreaterOrEqualParam implements ProcessRule
{
    use CheckFloat;

    public function __construct(
        private string $paramToCompareAgainst
    ) {
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkFloat($value);

        if ($processedValues->hasValue($this->paramToCompareAgainst) !== true) {
            $message = sprintf(
                Messages::ERROR_NO_PREVIOUS_PARAMETER,
                $this->paramToCompareAgainst
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        $previousValue = $processedValues->getValue($this->paramToCompareAgainst);
        $previousValue = $this->checkFloat($previousValue);

        if ($value < $previousValue) {
            $message = sprintf(
                Messages::VALUE_MUST_BE_GREATER_OR_EQUAL_TO_PARAM,
                $this->paramToCompareAgainst
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $message = sprintf(
            Messages::VALUE_MUST_BE_GREATER_OR_EQUAL_TO_PARAM,
            $this->paramToCompareAgainst
        );

        $paramDescription->setDescription($message);
    }
}
