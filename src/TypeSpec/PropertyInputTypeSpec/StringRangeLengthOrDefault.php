<?php

namespace TypeSpec\PropertyInputTypeSpec;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\HasDataType;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\RangeStringLength;

#[\Attribute]
/**
 * Gets a string by name from input, and checks it for minimum
 * and maximum length. If input value is not set for that name,
 * then a default value is used instead.
 */
class StringRangeLengthOrDefault implements HasDataType
{
    public function __construct(
        private int $minimumLength,
        private int $maximumLength,
        private string $name,
        private string $default
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetStringOrDefault($this->default),
            new RangeStringLength($this->minimumLength, $this->maximumLength),
        );
    }
}
