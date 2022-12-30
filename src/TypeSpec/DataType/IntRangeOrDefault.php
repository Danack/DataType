<?php

namespace TypeSpec\DataType;

use TypeSpec\ExtractRule\GetIntOrDefault;
use TypeSpec\HasDataType;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\RangeIntValue;

#[\Attribute]
/**
 * Gets an int by name from input, and checks it for minimum
 * and maximum values. If input value is not set for that name,
 * then a default value is used instead.
 */
class IntRangeOrDefault implements HasDataType
{
    public function __construct(
        private int $minimum,
        private int $maximum,
        private string $name,
        private int $default
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetIntOrDefault($this->default),
            new RangeIntValue($this->minimum, $this->maximum),
        );
    }
}
