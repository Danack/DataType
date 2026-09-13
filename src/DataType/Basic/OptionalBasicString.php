<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional string input. When the parameter is missing, the property receives null.
 */
#[\Attribute]
class OptionalBasicString implements HasInputType
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
            new GetOptionalString(),
            new SkipIfNull(),
            new MaxLength($this->maxLength),
        );
    }
}
