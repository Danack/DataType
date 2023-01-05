<?php

namespace DataType\InputType;

use DataType\ExtractRule\GetIntOrDefault;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeIntValue;

#[\Attribute]
/**
 * Gets an int by name from input, and checks it for minimum
 * and maximum values. If input value is not set for that name,
 * then a default value is used instead.
 */
class IntRangeOrDefault implements HasInputType
{
    public function __construct(
        private int $minimum,
        private int $maximum,
        private string $name,
        private int $default
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetIntOrDefault($this->default),
            new RangeIntValue($this->minimum, $this->maximum),
        );
    }
}
