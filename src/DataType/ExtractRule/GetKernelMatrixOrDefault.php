<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Exception\LogicExceptionData;
use DataType\Exception\JsonDecodeException;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;
use function DataType\json_decode_safe;

/**
 * Extracts a 2d matrix (aka array of arrays) of floating point values.
 */
class GetKernelMatrixOrDefault implements ExtractRule
{
    private ?array $default;

    /**
     * @param array $default
     */
    public function __construct(array $default)
    {
        foreach ($default as $row) {
            if (is_array($row) !== true) {
                throw new LogicExceptionData(Messages::MATRIX_INVALID_BAD_ROW);
            }

            foreach ($row as $value) {
                if (is_float($value) === false && is_int($value) === false) {
                    throw new LogicExceptionData(Messages::MATRIX_INVALID_BAD_CELL);
                }
            }
        }

        $this->default = $default;
    }

    /**
     * @throws JsonDecodeException
     * @throws LogicExceptionData
     */
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $currentValue = $dataStorage->getCurrentValue();

        if (is_string($currentValue) !== true) {
            throw new LogicExceptionData(Messages::BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE);
        }

        // TODO - this needs to be replaced with something that gives the
        // precise location of the error....probably.
        $matrix_value = json_decode_safe($currentValue);

        if (is_array($matrix_value) !== true) {
            $message = sprintf(
                Messages::KERNEL_MATRIX_ARRAY_EXPECTED,
                var_export($matrix_value, true)
            );
            return ValidationResult::errorResult($dataStorage, $message);
        }

        $row_count = 0;

        foreach ($matrix_value as $row) {
            if (is_array($row) !== true) {
                $message = sprintf(
                    Messages::KERNEL_MATRIX_ERROR_AT_ROW_2D_EXPECTED,
                    $row_count
                );

                return ValidationResult::errorResult($dataStorage, $message);
            }

            $column_count = 0;
            foreach ($row as $value) {
                if (is_float($value) === false && is_int($value) === false) {
                    $message = sprintf(
                        Messages::KERNEL_MATRIX_ERROR_AT_ROW_COLUMN_NUMBER_EXPECTED,
                        $row_count,
                        $column_count,
                    );

                    return ValidationResult::errorResult($dataStorage, $message);
                }

                $column_count += 1;
            }

            $row_count += 1;
        }

        return ValidationResult::valueResult($matrix_value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_ARRAY);
        $paramDescription->setFormat('kernel_matrix');
    }
}
