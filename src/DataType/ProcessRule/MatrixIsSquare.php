<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that a matrix is square, i.e. has the same number of rows
 * and columns.
 */
class MatrixIsSquare implements ProcessRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        /** @var array<array<float>> $value */

        $rowsDiminsion = count($value);
        $columnsDimension = count($value[0]);

        if ($rowsDiminsion !== $columnsDimension) {
            $message = sprintf(
                Messages::MATRIX_MUST_BE_SQUARE,
                $rowsDiminsion,
                $columnsDimension
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
//        $paramDescription->setMaxLength($this->maxLength);
    }
}
