<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;

/**
 * Extracts an array of DateTime values or null if the parameter is not available.
 */
class GetArrayOfDatetimeOrNull implements ExtractRule
{
    private GetArrayOfDatetime $getArrayOfDatetime;

    /**
     * @param string[]|null $allowedFormats
     * @param ProcessRule ...$rules
     */
    public function __construct(?array $allowedFormats = null, ProcessRule ...$rules)
    {
        $this->getArrayOfDatetime = new GetArrayOfDatetime($allowedFormats, ...$rules);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return $this->getArrayOfDatetime->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $this->getArrayOfDatetime->updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
