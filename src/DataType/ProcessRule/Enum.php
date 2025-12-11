<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that the value is one of a known set of values
 */
class Enum implements ProcessRule
{
    /**
     * @var array<string>
     */
    private array $allowedValues;

    /**
     *
     * @param array<string> $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
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
        if (in_array($value, $this->allowedValues, true) !== true) {
            $message = sprintf(
                Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
                var_export($value, true), // This is sub-optimal
                implode(', ', $this->allowedValues)
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
        $allowedValues = array_values($this->allowedValues);
        /** @var array<int, mixed> $allowedValues */
        $paramDescription->setEnum($allowedValues);
    }
}
