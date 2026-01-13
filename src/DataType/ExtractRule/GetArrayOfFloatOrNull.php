<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;

/**
 * Extracts an array of float values or null if the parameter is not available.
 */
class GetArrayOfFloatOrNull implements ExtractRule
{
    private GetArrayOfFloat $getArrayOfFloat;

    public function __construct(ProcessRule ...$rules)
    {
        $this->getArrayOfFloat = new GetArrayOfFloat(...$rules);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return $this->getArrayOfFloat->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $this->getArrayOfFloat->updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
