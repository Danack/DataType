<?php

declare(strict_types = 1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Extracts an array of a <DataType|null>.
 */
class GetArrayOfTypeOrNull extends GetArrayOfType implements ExtractRule
{
    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return parent::process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        parent::updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
