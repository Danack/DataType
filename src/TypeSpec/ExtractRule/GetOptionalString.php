<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Extracts a string value or null if the parameter is not available.
 */
class GetOptionalString implements ExtractPropertyRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        $value = $dataStorage->getCurrentValue();
        if (is_string($value) !== true) {
            $message = sprintf(
                Messages::STRING_EXPECTED,
                gettype($value)
            );
            return ValidationResult::errorResult($dataStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(false);
    }
}
