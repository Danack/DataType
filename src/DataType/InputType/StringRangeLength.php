<?php

namespace DataType\InputType;

use DataType\ExtractRule\GetString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeStringLength;

#[\Attribute]
/**
 * Gets a string by name from input, and checks it for minimum
 * and maximum length.
 */
class StringRangeLength implements HasInputType
{
    public function __construct(
        private int $minimumLength,
        private int $maximumLength,
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new RangeStringLength($this->minimumLength, $this->maximumLength),
        );
    }
}
