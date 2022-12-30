<?php

namespace TypeSpec\DataType;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\HasDataType;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\RangeIntValue;

#[\Attribute]
/**
 * Gets an int by name from input, and checks it for minimum
 * and maximum values.
 */
class IntRange implements HasDataType
{
    /**
     *
     * @param int $minimum The minimum value, inclusive.
     * @param int $maximum The maximum value, inclusive
     * @param string $name
     */
    public function __construct(
        private int $minimum,
        private int $maximum,
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetInt(),
            new RangeIntValue($this->minimum, $this->maximum),
        );
    }
}
