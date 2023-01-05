<?php

namespace DataType\InputType;

use DataType\ExtractRule\GetInt;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeIntValue;

#[\Attribute]
/**
 * Gets an int by name from input, and checks it for minimum
 * and maximum values.
 */
class IntRange implements HasInputType
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

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetInt(),
            new RangeIntValue($this->minimum, $this->maximum),
        );
    }
}
