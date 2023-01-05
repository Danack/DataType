<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Check Matrix is odd sized. This is useful as some operation require
 * a matrix that has a clearly defined centre value.
 */
class MatrixIsOddSized implements ProcessPropertyRule
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
