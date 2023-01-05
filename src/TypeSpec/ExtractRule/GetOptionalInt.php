<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\CastToInt;
use TypeSpec\ValidationResult;

/**
 * Extracts a int point value or null if the parameter is not available.
 */
class GetOptionalInt implements ExtractPropertyRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        $intRule = new CastToInt();
        return $intRule->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(false);
    }
}
