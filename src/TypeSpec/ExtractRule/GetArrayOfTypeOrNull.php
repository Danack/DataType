<?php

declare(strict_types = 1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Extracts an array of a <DataType|null>.
 */
class GetArrayOfTypeOrNull extends GetArrayOfType implements ExtractPropertyRule
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
