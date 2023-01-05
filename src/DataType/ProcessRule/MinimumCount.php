<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Exception\LogicExceptionData;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that there are at least a set count of entries.
 */
class MinimumCount implements ProcessRule
{
    private int $minimumCount;

    /**
     * @param int $minimumCount the minimum number (inclusive) of elements.
     */
    public function __construct(int $minimumCount)
    {
        if ($minimumCount < 0) {
            throw new LogicExceptionData(Messages::ERROR_MINIMUM_COUNT_MINIMUM);
        }

        $this->minimumCount = $minimumCount;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if (is_array($value) !== true) {
            $message = sprintf(
                Messages::ERROR_WRONG_TYPE,
                gettype($value)
            );

            throw new LogicExceptionData($message);
        }

        $actualCount = count($value);

        if ($actualCount < $this->minimumCount) {
            $message = sprintf(
                Messages::ERROR_TOO_FEW_ELEMENTS,
                $this->minimumCount,
                $actualCount
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinItems($this->minimumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
