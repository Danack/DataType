<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Rule that ends processing with a set value.
 *
 * Used for testing.
 */
class AlwaysEndsRule implements ProcessRule
{
    /** @var mixed */
    private $finalValue;

    /**
     * @param mixed $finalResult
     */
    public function __construct($finalResult)
    {
        $this->finalValue = $finalResult;
    }

    /**
     * @param mixed $value
     * @param ProcessedValues $processedValues
     * @param DataStorage $inputStorage
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        return ValidationResult::finalValueResult($this->finalValue);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing.
    }
}
