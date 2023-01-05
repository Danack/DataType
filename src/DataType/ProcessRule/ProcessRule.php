<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\ProcessedValues;
use DataType\Rule;
use DataType\ValidationResult;

/**
 * A rule for processing the value after it has been extracted from the
 * InputStorage by an ExtractDataTypeRule.
 */
interface ProcessRule extends Rule
{
    /**
     * @param mixed $value The current value of the param as it is being processed.
     * @param ProcessedValues $processedValues The already processed parameters.
     * @param DataStorage $inputStorage The InputStorage with the current path set to the
     *   appropriate place to find the current value by calling $inputStorage->getCurrentValue()
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult;
}
