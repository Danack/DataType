<?php

namespace TypeSpec\PropertyInputTypeSpec;

use TypeSpec\ExtractRule\GetString;
use TypeSpec\HasDataType;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\RangeIntValue;
use TypeSpec\ProcessRule\RangeStringLength;

#[\Attribute]
/**
 * Gets a string by name from input, and checks it for minimum
 * and maximum length.
 */
class StringRangeLength implements HasDataType
{
    public function __construct(
        private int $minimumLength,
        private int $maximumLength,
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetString(),
            new RangeStringLength($this->minimumLength, $this->maximumLength),
        );
    }
}
