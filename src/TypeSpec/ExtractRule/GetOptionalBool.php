<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\CastToBool;
use TypeSpec\ValidationResult;

/**
 * Extracts a boolean. If a parameter is not set, then the value is null.
 *
 * bool(true) - true
 * bool(false) - false
 * string(true) - true
 * string(false) - false
 * any other input - error
 */
class GetOptionalBool implements ExtractPropertyRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        $intRule = new CastToBool();
        return $intRule->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(false);
    }
}
