<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Check Matrix is odd sized. This is useful as some operation require
 * a matrix that has a clearly defined centre value.
 */
class MatrixIsOddSized implements ProcessRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        /** @var array<array<float>> $value   */
        $outerDimension = count($value);

        if (($outerDimension % 2) === 0) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::MATRIX_MUST_BE_ODD_SIZED_ROWS_ARE_EVEN
            );
        }

        $innerDimension = count($value[0]);

        if (($innerDimension % 2) === 0) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::MATRIX_MUST_BE_ODD_SIZED_COLUMNS_ARE_EVEN
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
//        $paramDescription->setMaxLength($this->maxLength);
    }
}
