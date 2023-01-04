<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use JsonSafe\JsonDecodeException;
use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Exception\LogicException;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;
use function JsonSafe\json_decode_safe;

class GetKernelMatrixOrDefault implements ExtractPropertyRule
{
    private ?array $default;

    /**
     * @param array $default
     */
    public function __construct(array $default)
    {
        foreach ($default as $row) {
            if (is_array($row) !== true) {
                throw new LogicException(Messages::MATRIX_INVALID_BAD_ROW);
            }

            foreach ($row as $value) {
                if (is_float($value) === false && is_int($value) === false) {
                    throw new LogicException(Messages::MATRIX_INVALID_BAD_CELL);
                }
            }
        }

        $this->default = $default;
    }

    /**
     * @throws JsonDecodeException
     * @throws LogicException
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
            throw new LogicException(Messages::BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE);
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
//        $paramDescription->setDefault($this->default);
//        $paramDescription->setRequired(false);
    }
}
