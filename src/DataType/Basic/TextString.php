<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;

/**
 * Required string input. Alias for BasicString with the same behaviour.
 */
#[\Attribute]
class TextString implements HasInputType
{
    public function __construct(
        private string $name,
        private int $maxLength = 1_000_000,
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new MaxLength($this->maxLength),
        );
    }
}
