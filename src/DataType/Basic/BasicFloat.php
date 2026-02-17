<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetFloat;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required float input.
 */
#[\Attribute]
class BasicFloat implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetFloat(),
        );
    }
}
