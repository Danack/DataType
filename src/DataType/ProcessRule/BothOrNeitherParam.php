<?php

declare(strict_types=1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that this parameter and another parameter are either both set (non-null) or both missing (null).
 * Passing just one of the pair is an error.
 */
class BothOrNeitherParam implements ProcessRule
{
    public function __construct(
        private string $paramToPairWith
    ) {
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $currentParamPath = $inputStorage->getPath();

        if ($processedValues->hasValue($this->paramToPairWith) !== true) {
            $message = sprintf(
                Messages::PAIR_PARAM_BOTH_OR_NEITHER,
                $this->paramToPairWith,
                $currentParamPath
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        $otherValue = $processedValues->getValue($this->paramToPairWith);
        $currentIsNull = $value === null;
        $otherIsNull = $otherValue === null;

        if ($currentIsNull !== $otherIsNull) {
            $message = sprintf(
                Messages::PAIR_PARAM_BOTH_OR_NEITHER,
                $this->paramToPairWith,
                $currentParamPath
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $message = sprintf(
            Messages::PAIR_PARAM_BOTH_OR_NEITHER,
            $this->paramToPairWith,
            'this parameter'
        );

        $paramDescription->setDescription($message);
    }
}
