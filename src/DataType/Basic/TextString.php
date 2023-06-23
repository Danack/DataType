<?php

namespace DataType\Basic;

use DataType\InputType;
use DataType\HasInputType;
use DataType\ExtractRule\GetString;

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
