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
 * Validates that there are at most a known count of entries.
 */
class MaximumCount implements ProcessRule
{
    private int $maximumCount;

    /**
     *
     * @param int $maximumCount The maximum number (inclusive) of elements
     */
    public function __construct(int $maximumCount)
    {
        if ($maximumCount < 0) {
            throw new LogicExceptionData(Messages::ERROR_MAXIMUM_COUNT_MINIMUM);
        }

        $this->maximumCount = $maximumCount;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if (is_array($value) !== true) {
            $message = sprintf(
                Messages::ERROR_WRONG_TYPE_VARIANT_1,
                gettype($value)
            );

            throw new LogicExceptionData($message);
        }

        $actualCount = count($value);

        if ($actualCount > $this->maximumCount) {
            $message = sprintf(
                Messages::ERROR_TOO_MANY_ELEMENTS,
                $this->maximumCount,
                $actualCount
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinItems($this->maximumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
