<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\Exception\InvalidRulesExceptionData;

trait CheckFloat
{
    public function checkFloat(mixed $value): float
    {
        if (\is_float($value)) {
            return $value;
        }

        throw InvalidRulesExceptionData::expectsFloatForProcessing(static::class);
    }
}
