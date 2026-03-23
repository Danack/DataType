<?php

declare(strict_types=1);

namespace DataType\Exception;

/**
 * DataTypeLogicException - this represents something a programmer has
 * done wrong, and can't be handled locally in code.
 *
 * There may be some edge-cases (e.g. if you allow users to input the date
 * format that values should be parsed as, and forget to check the format is
 * valid before passing it to this library)
 *
 * @unchecked
 */
class DataTypeLogicException extends \Exception
{
    public const ONLY_KEYS = "Processed values must have string keys";

    public const ONLY_INT_KEYS = "Key for array must be integer";

    public const MISSING_VALUE = "Trying to access [%s] which isn't present in ParamValuesImpl.";

    public const NOT_VALIDATION_PROBLEM = "Array must contain only 'ValidationProblem's instead got [%s]";

    public const ONLY_PROCESSED_VALUES = "Processed values must all be instances of ProcessedValue.";

    public static function keysMustBeStrings(): self
    {
        return new self(self::ONLY_KEYS);
    }

    public static function onlyProcessedValues(): self
    {
        return new self(self::ONLY_PROCESSED_VALUES);
    }

    /**
     * @param mixed $wrongType
     * @return self
     */
    public static function onlyValidationProblemsAllowed($wrongType): self
    {
        return new self(sprintf(self::NOT_VALIDATION_PROBLEM, gettype($wrongType)));
    }

    public static function keysMustBeIntegers(): self
    {
        return new self(self::ONLY_INT_KEYS);
    }

    public static function missingValue(string $name): self
    {
        return new self(sprintf(self::MISSING_VALUE, $name));
    }
}
