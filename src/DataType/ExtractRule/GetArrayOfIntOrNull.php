<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;

/**
 * Extracts an array of int values or null if the parameter is not available.
 */
class GetArrayOfIntOrNull implements ExtractRule
{
    private GetArrayOfInt $getArrayOfInt;

    public function __construct(ProcessRule ...$rules)
    {
        $this->getArrayOfInt = new GetArrayOfInt(...$rules);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return $this->getArrayOfInt->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $this->getArrayOfInt->updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
