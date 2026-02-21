<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\Exception\InvalidRulesExceptionData;

trait CheckInt
{
    public function checkInt(mixed $value): int
    {
        if (\is_scalar($value) || $value === null) {
            return (int) $value;
        }

        throw InvalidRulesExceptionData::expectsIntForProcessing(static::class);
    }

    /**
     * @return int|string
     */
    public function checkIntOrString(mixed $value): int|string
    {
        if (\is_int($value) || \is_string($value)) {
            return $value;
        }

        throw InvalidRulesExceptionData::expectsIntForProcessing(static::class);
    }
}
