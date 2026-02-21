<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\Exception\InvalidRulesExceptionData;

trait CheckInt
{
    public function checkInt(mixed $value): int
    {
        if (\is_int($value)) {
            return $value;
        }

        throw InvalidRulesExceptionData::expectsIntForProcessing(static::class);
    }
}
