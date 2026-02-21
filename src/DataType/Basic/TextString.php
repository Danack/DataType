<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetString;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required string input. Alias for BasicString with the same behaviour.
 */
#[\Attribute]
class TextString implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
        );
    }
}
