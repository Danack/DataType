<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;

/**
 * Extracts an array of bool values or null if the parameter is not available.
 */
class GetArrayOfBoolOrNull implements ExtractRule
{
    private GetArrayOfBool $getArrayOfBool;

    public function __construct(ProcessRule ...$rules)
    {
        $this->getArrayOfBool = new GetArrayOfBool(...$rules);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return $this->getArrayOfBool->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $this->getArrayOfBool->updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
