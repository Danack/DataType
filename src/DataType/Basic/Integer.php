<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetInt;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required integer input.
 */
#[\Attribute]
class Integer implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetInt(),
        );
    }
}
