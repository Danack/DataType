<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;

/**
 * Extracts an array of string values or null if the parameter is not available.
 */
class GetArrayOfStringOrNull implements ExtractRule
{
    private GetArrayOfString $getArrayOfString;

    public function __construct(ProcessRule ...$rules)
    {
        $this->getArrayOfString = new GetArrayOfString(...$rules);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return $this->getArrayOfString->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $this->getArrayOfString->updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
