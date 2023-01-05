<?php

declare(strict_types = 1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\CastToBool;
use TypeSpec\ValidationResult;

/**
 * Extracts a boolean value.
 *
 * bool(true) - true
 * bool(false) - false
 * string(true) - true
 * string(false) - false
 * any other input - error
 */
class GetBool implements ExtractPropertyRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $rule = new CastToBool();

        return $rule->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(true);
    }
}
