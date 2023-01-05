<?php

declare(strict_types = 1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\ProcessedValues;
use DataType\Rule;
use DataType\ValidationResult;

/**
 * The first rule for a property of a type. It should extract the
 * initial value out of the InputStorage.
 *
 */
interface ExtractRule extends Rule
{
    /**
     * @param ProcessedValues $processedValues
     * @param DataStorage $dataStorage
     * @return ValidationResult
     */
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult;
}
