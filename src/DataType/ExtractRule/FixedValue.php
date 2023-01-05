<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Extracts a fixed value. Useful for testing.
 */
class FixedValue implements ExtractRule
{
    private mixed $value;

    /**
     * @param mixed $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {

        return ValidationResult::valueResult($this->value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
    }
}
