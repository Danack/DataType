<?php

declare(strict_types = 1);

namespace DataTypeTest\Integration;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationProblem;
use DataType\ValidationResult;

/**
 * Example of processing an array, without processing each item individually
 * as a separate type.
 *
 * @coversNothing
 */
class ArrayAllMultiplesOf implements ProcessRule
{
    private int $multiplicand;

    /**
     *
     * @param int $multiplicand
     */
    public function __construct(int $multiplicand)
    {
        $this->multiplicand = $multiplicand;
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
        $errors = [];

        $index = 0;
        foreach ($value as $item) {
            if (($item % $this->multiplicand) !== 0) {
                // Because this is operating on an array of items, we need to put the complete name
                // not just the index
                $message = sprintf(
                    'Value at position [%d] is not a multiple of %s but has value [%s]',
                    $index,
                    $this->multiplicand,
                    $item
                );

                $errors[] = new ValidationProblem($inputStorage, $message);
            }
            $index += 1;
        }

        if (count($errors) !== 0) {
            return ValidationResult::fromValidationProblems($errors);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        throw new \Exception("updateParamDescription not implemented yet.");
    }
}
