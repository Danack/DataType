<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Tri-state optional string: absent, explicit null, or present string (then process rules apply).
 */
class GetOptionalNullableString implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::finalValueResult(Absent::instance());
        }

        $value = $dataStorage->getCurrentValue();
        if ($value === null) {
            return ValidationResult::finalValueResult(PresentNull::instance());
        }

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
        $paramDescription->setNullAllowed(true);
    }
}
