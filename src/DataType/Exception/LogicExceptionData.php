<?php

declare(strict_types=1);

namespace DataType\Exception;

/**
 * Class LogicException
 * You have called something that has no meaning.
 */
class LogicExceptionData extends DataTypeException
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
