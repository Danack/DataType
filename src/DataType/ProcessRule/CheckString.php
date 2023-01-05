<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\Exception\InvalidRulesExceptionData;

trait CheckString
{
    public function checkString(mixed $value): string
    {
        if (is_string($value) === true) {
            return $value;
        }

        if (is_object($value) === true &&
            is_a($value, \Stringable::class) === true) {
            return (string)$value;
        }

        throw InvalidRulesExceptionData::expectsStringForProcessing(get_called_class());
    }
}
