<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\CastToBool;
use DataType\ValidationResult;

/**
 * Extracts a boolean value or null.
 *
 * bool(true) - true
 * bool(false) - false
 * string(true) - true
 * string(false) - false
 * null - null
 * any other input - error
 *
 * This pattern is probably 'not best practice' for an API, and just not including the key/value
 * is possibly better to indicate a lack of an optional value.
 */
class GetBoolOrNull implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $value = $dataStorage->getCurrentValue();

        if (is_null($value) === true) {
            return ValidationResult::valueResult($value);
        }

        $boolRule = new CastToBool();
        return $boolRule->process($value, $processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(true);
        $paramDescription->setNullAllowed(true);
    }
}
