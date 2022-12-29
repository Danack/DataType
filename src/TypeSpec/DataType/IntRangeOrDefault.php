<?php

namespace TypeSpec\DataType;

use TypeSpec\ExtractRule\GetIntOrDefault;
use TypeSpec\HasDataType;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\RangeIntValue;

#[\Attribute]
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
