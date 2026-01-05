<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;
use DataType\ProcessRule\ProcessRule;
use function DataType\getEnumCases;
use function DataType\getEnumCaseValues;

/**
 * Checks that the value is one of a known set of values
 */
class PhpEnum implements ProcessRule
{
//    /**
//     * @var \BackedEnum[]
//     */
//    private array $allowedValues;

    /**
     * @param \BackedEnum[] $allowedValues
     */


    /**
     * @param class-string<\BackedEnum> $enum_type
     */
    public function __construct(private string $enum_type)
    {
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
        $allowedValues = getEnumCases($this->enum_type);

        // TODO - could check if $value is the same type as the
        // BackedEnum

        $match = null;
        foreach ($allowedValues as $allowedValue) {
            if ($allowedValue->value === $value) {
                $match = $allowedValue;
            }
        }

        if ($match === null) {
            $allowedValues = getEnumCaseValues($this->enum_type);

            $message = sprintf(
                Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
                var_export($value, true), // This is sub-optimal
                implode(', ', $allowedValues)
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        return ValidationResult::valueResult($match->value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $allowedValues = getEnumCaseValues($this->enum_type);
        $paramDescription->setEnum($allowedValues);
    }
}
