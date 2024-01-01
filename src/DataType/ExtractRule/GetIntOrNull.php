<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\CastToInt;
use DataType\ValidationResult;

/**
 * Extracts a int value.
 *
 * This pattern is probably 'not best practice' for an API, and just not including the key/value
 * is possibly better to indicate a lack of an optional value.
 */
class GetIntOrNull implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $value = $dataStorage->getCurrentValue();

        if (is_null($value) === true) {
            return ValidationResult::valueResult($value);
        }

        $intRule = new CastToInt();
        return $intRule->process($value, $processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
        $paramDescription->setNullAllowed(true);
    }
}
