<?php

namespace DataType\InputType;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeStringLength;

#[\Attribute]
/**
 * Gets a string by name from input, and checks it for minimum
 * and maximum length. If input value is not set for that name,
 * then a default value is used instead.
 */
class StringRangeLengthOrDefault implements HasInputType
{
    public function __construct(
        private int $minimumLength,
        private int $maximumLength,
        private string $name,
        private string $default
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault($this->default),
            new RangeStringLength($this->minimumLength, $this->maximumLength),
        );
    }
}
